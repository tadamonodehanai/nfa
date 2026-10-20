<?php
/**
 * 委員会ごとの権限。
 *
 *   管理者（制作側）… すべて
 *   事務局（editor） … 全委員会の記事・大会資料、固定ページ、協賛企業、「重要」「TOPに掲載」、レビュー待ちの承認
 *   委員会担当（nfa_committee_editor）… 自分の委員会のお知らせ・大会資料の作成・編集・公開、自分が上げたメディア
 *
 * 委員会担当には、ユーザーごとに「担当委員会」（複数可）を割り当てる。
 * 投稿の担当委員会は保存時に自動で付け、担当外の委員会には変えさせない。
 * 他の委員会の記事は、一覧に出さず、URLを直接開いても編集できない（map_meta_cap で判定）。
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

class NFA_Com_Access {

	const ROLE      = 'nfa_committee_editor';
	const USER_META = 'nfa_committees';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'maybe_install_roles' ) );
		add_filter( 'map_meta_cap', array( __CLASS__, 'map_meta_cap' ), 10, 4 );
		add_filter( 'user_has_cap', array( __CLASS__, 'review_mode' ), 10, 4 );
		add_action( 'pre_get_posts', array( __CLASS__, 'filter_admin_lists' ) );
		add_filter( 'ajax_query_attachments_args', array( __CLASS__, 'own_media_only' ) );
		add_action( 'save_post', array( __CLASS__, 'assign_on_save' ), 20, 2 );
		add_filter( 'rest_' . NFA_Com_Committees::TAX . '_query', array( __CLASS__, 'limit_term_choices' ), 10, 2 );
		add_filter( 'get_terms_args', array( __CLASS__, 'limit_term_choices_admin' ), 10, 2 );
		add_action( 'admin_menu', array( __CLASS__, 'hide_menus' ), 999 );
		add_action( 'show_user_profile', array( __CLASS__, 'user_fields' ) );
		add_action( 'edit_user_profile', array( __CLASS__, 'user_fields' ) );
		add_action( 'personal_options_update', array( __CLASS__, 'save_user_fields' ) );
		add_action( 'edit_user_profile_update', array( __CLASS__, 'save_user_fields' ) );
	}

	/* ---------- 役割 ---------- */

	public static function maybe_install_roles() {
		if ( get_option( 'nfa_com_roles' ) !== NFA_COM_VERSION ) {
			self::install_roles();
		}
	}

	public static function install_roles() {
		remove_role( self::ROLE );
		add_role(
			self::ROLE,
			'委員会担当',
			array(
				'read'                   => true,
				'upload_files'           => true,
				'edit_posts'             => true,
				'edit_published_posts'   => true,
				'edit_others_posts'      => true,   // 同じ委員会の他の人の記事。委員会外は map_meta_cap で弾く
				'publish_posts'          => true,   // 公開の流れ B のときは review_mode で外す
				'delete_posts'           => true,
				'delete_published_posts' => true,
				'delete_others_posts'    => true,
				'assign_terms'           => true,
			)
		);
		foreach ( array( 'administrator', 'editor' ) as $r ) {
			$role = get_role( $r );
			if ( $role ) {
				$role->add_cap( NFA_Com_Flags::CAP );
				$role->add_cap( 'nfa_manage_sponsors' );
			}
		}
		update_option( 'nfa_com_roles', NFA_COM_VERSION, false );
	}

	public static function is_committee_user( $user_id = 0 ) {
		$user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
		return $user && $user->exists() && in_array( self::ROLE, (array) $user->roles, true ) && ! user_can( $user, 'manage_options' );
	}

	/**
	 * ユーザーの担当委員会（用語ID）。
	 *
	 * @return int[]
	 */
	public static function user_committees( $user_id = 0 ) {
		$user_id = $user_id ? $user_id : get_current_user_id();
		$ids     = array_values( array_filter( array_map( 'intval', (array) get_user_meta( $user_id, self::USER_META, true ) ) ) );
		// 事務局の担当は「協会」（情報発信元としての協会）の記事も書ける
		$office = get_term_by( 'slug', 'office', NFA_Com_Committees::TAX );
		$assoc  = get_term_by( 'slug', NFA_Com_Committees::ASSOCIATION, NFA_Com_Committees::TAX );
		if ( $office && $assoc && in_array( (int) $office->term_id, $ids, true ) ) {
			$ids[] = (int) $assoc->term_id;
		}
		return array_values( array_unique( $ids ) );
	}

	private static function managed_type( $type ) {
		return in_array( $type, array( 'post', NFA_Com_Doc_Type::TYPE ), true );
	}

	private static function post_committees( $post_id ) {
		$ids = wp_get_object_terms( $post_id, NFA_Com_Committees::TAX, array( 'fields' => 'ids' ) );
		return is_wp_error( $ids ) ? array() : array_map( 'intval', $ids );
	}

	/* ---------- 権限の判定 ---------- */

	/**
	 * 委員会担当が、担当外の委員会の記事を編集・削除・公開しようとしたら弾く。
	 */
	public static function map_meta_cap( $caps, $cap, $user_id, $args ) {
		if ( ! in_array( $cap, array( 'edit_post', 'delete_post', 'publish_post', 'read_post' ), true ) || empty( $args[0] ) ) {
			return $caps;
		}
		if ( ! self::is_committee_user( $user_id ) ) {
			return $caps;
		}
		$post = get_post( $args[0] );
		if ( ! $post || ! self::managed_type( $post->post_type ) ) {
			// 固定ページなどは、役割の権限（持っていない）どおり
			return $caps;
		}
		if ( 'read_post' === $cap && 'publish' === $post->post_status ) {
			return $caps;
		}
		$mine  = self::user_committees( $user_id );
		$terms = self::post_committees( $post->ID );
		// 作成直後でまだ委員会が付いていない自分の下書きは許可する（保存時に自分の委員会が付く）
		if ( ! $terms && (int) $post->post_author === (int) $user_id ) {
			return $caps;
		}
		if ( ! array_intersect( $mine, $terms ) ) {
			return array( 'do_not_allow' );
		}
		return $caps;
	}

	/**
	 * 公開の流れ B（当面は事務局が承認）のときは、委員会担当から公開の権限を外す（レビュー待ちとして送信になる）。
	 */
	public static function review_mode( $allcaps, $caps, $args, $user ) {
		if ( 'review' === NFA_Com_Settings::value( 'publish_flow' ) && in_array( self::ROLE, (array) $user->roles, true ) && empty( $allcaps['manage_options'] ) ) {
			$allcaps['publish_posts'] = false;
		}
		return $allcaps;
	}

	/* ---------- 一覧・メディア ---------- */

	public static function filter_admin_lists( $q ) {
		if ( ! is_admin() || ! $q->is_main_query() || ! self::is_committee_user() ) {
			return;
		}
		global $pagenow;
		$type = $q->get( 'post_type' ) ? $q->get( 'post_type' ) : 'post';
		if ( 'edit.php' === $pagenow && self::managed_type( $type ) ) {
			$mine = self::user_committees();
			$q->set(
				'tax_query',
				array(
					'relation' => 'OR',
					array( 'taxonomy' => NFA_Com_Committees::TAX, 'terms' => $mine ? $mine : array( 0 ), 'field' => 'term_id' ),
				)
			);
		}
		if ( 'upload.php' === $pagenow ) {
			$q->set( 'author', get_current_user_id() );
		}
	}

	public static function own_media_only( $query ) {
		if ( self::is_committee_user() ) {
			$query['author'] = get_current_user_id();
		}
		return $query;
	}

	/* ---------- 担当委員会の自動付与 ---------- */

	/**
	 * 委員会担当が保存したら、担当委員会を自分の担当の中に限る（なければ最初の担当委員会）。
	 * 対象が未選択なら、委員会の初期値（例：4種 → 小学生）を入れる。
	 */
	public static function assign_on_save( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || ! self::managed_type( $post->post_type ) ) {
			return;
		}
		$terms = self::post_committees( $post_id );
		if ( self::is_committee_user() ) {
			$mine  = self::user_committees();
			$keep  = array_values( array_intersect( $terms, $mine ) );
			$keep  = $keep ? $keep : array_slice( $mine, 0, 1 );
			if ( $keep !== $terms ) {
				wp_set_object_terms( $post_id, $keep, NFA_Com_Committees::TAX );
			}
			$terms = $keep;
		}
		NFA_Com_Committees::apply_default_target( $post_id, $terms );
	}

	/**
	 * 投稿画面の「担当委員会」の選択肢を、自分の担当だけにする（ブロックエディター）。
	 */
	public static function limit_term_choices( $args, $request ) {
		if ( self::is_committee_user() ) {
			$args['include'] = self::user_committees() ? self::user_committees() : array( 0 );
		}
		return $args;
	}

	/**
	 * 同じく、クラシックな画面（メタボックス・一覧の絞り込み）。
	 */
	public static function limit_term_choices_admin( $args, $taxonomies ) {
		if ( is_admin() && in_array( NFA_Com_Committees::TAX, (array) $taxonomies, true ) && self::is_committee_user() && empty( $args['object_ids'] ) ) {
			$args['include'] = self::user_committees() ? self::user_committees() : array( 0 );
		}
		return $args;
	}

	/* ---------- メニュー ---------- */

	public static function hide_menus() {
		if ( ! self::is_committee_user() ) {
			return;
		}
		foreach ( array( 'edit.php?post_type=page', 'themes.php', 'plugins.php', 'users.php', 'tools.php', 'options-general.php', 'edit-comments.php' ) as $slug ) {
			remove_menu_page( $slug );
		}
	}

	/* ---------- ユーザー画面：担当委員会 ---------- */

	public static function user_fields( $user ) {
		if ( ! current_user_can( 'promote_users' ) && ! current_user_can( 'edit_users' ) ) {
			// 本人には表示だけ
			$names = array_map( fn( $id ) => get_term( $id )->name ?? '', self::user_committees( $user->ID ) );
			printf( '<h2>担当委員会</h2><p>%s</p>', esc_html( $names ? implode( '・', $names ) : '未設定' ) );
			return;
		}
		$mine  = self::user_committees( $user->ID );
		$terms = get_terms( array( 'taxonomy' => NFA_Com_Committees::TAX, 'hide_empty' => false ) );
		wp_nonce_field( 'nfa_com_user', 'nfa_com_user_nonce' );
		echo '<h2>担当委員会</h2><table class="form-table" role="presentation"><tr><th>担当委員会</th><td>';
		foreach ( is_wp_error( $terms ) ? array() : $terms as $t ) {
			printf(
				'<label style="display:inline-block;margin:0 16px 8px 0"><input type="checkbox" name="nfa_committees[]" value="%d" %s> %s</label>',
				(int) $t->term_id,
				checked( in_array( (int) $t->term_id, $mine, true ), true, false ),
				esc_html( $t->name )
			);
		}
		echo '<p class="description">権限グループが「委員会担当」のユーザーは、ここで選んだ委員会のお知らせ・大会資料だけを作成・編集できます（複数可）。</p></td></tr></table>';
	}

	public static function save_user_fields( $user_id ) {
		if ( ! isset( $_POST['nfa_com_user_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nfa_com_user_nonce'] ), 'nfa_com_user' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}
		$ids = isset( $_POST['nfa_committees'] ) ? array_map( 'absint', (array) $_POST['nfa_committees'] ) : array();
		update_user_meta( $user_id, self::USER_META, array_values( array_filter( $ids ) ) );
	}
}
