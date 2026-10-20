<?php
/**
 * 過去の日程・試合結果（デザイン archive-cat-*.html）。/committee/{スラッグ}/archive/
 * 担当委員会の大会・リーグ資料（nfa_doc）を、最新の年度を除いて年度別に並べる（最新の年度は委員会ページ）。
 * 行は「区分 対象」ごとにまとめ、資料をボタンで並べる。
 *
 * @package NFA2026
 */

get_header();

$term  = get_queried_object();
$slug  = $term->slug;
$ja    = (string) nfa_term_meta( $term, 'ja' );
$ja    = $ja ? $ja : $term->name;
$tg    = (string) nfa_term_meta( $term, 'tg' );
$en    = (string) nfa_term_meta( $term, 'en' );
$years = array_slice( nfa_doc_years( $slug ), 1 );
$arrow = '<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span>';
?>
<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $ja ); ?></a><span aria-hidden="true">›</span><span aria-current="page">過去の日程・試合結果</span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><p aria-hidden="true" class="pg-en"><span class="ln"><span><?php echo esc_html( trim( $en . ' ARCHIVE' ) ); ?></span></span></p><h1 class="pg-ja">過去の日程・試合結果<span class="tg"><?php echo esc_html( $ja . ( $tg ? '・' . $tg : '' ) ); ?></span></h1></div><div class="pg-sub"><p>過去の大会・リーグの日程表、試合結果、星取表を年度別に掲載しています。<br class="pc"/>今年度の情報は<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $ja ); ?>のページ</a>をご覧ください。</p></div></div></section>
<?php if ( $years ) : ?>
<nav aria-label="年度" class="lnav arc-nav"><ul><?php foreach ( $years as $y ) : ?><li><a href="#y<?php echo (int) $y; ?>"><small>FY</small><?php echo (int) $y; ?>年度</a></li><?php endforeach; ?></ul></nav>
	<?php
	foreach ( $years as $y ) :
		$docs = nfa_docs( array( 'committee' => $slug, 'year' => $y ) );
		usort( $docs, fn( $a, $b ) => $a->ID <=> $b->ID );   // 取り込んだ順（旧サイトのページの並び）
		?>
<section class="blk arc-y" id="y<?php echo (int) $y; ?>"><div class="wrap">
<div class="bh"><div><div class="e">FY <i><?php echo (int) $y; ?></i></div><h2><span class="nw"><?php echo (int) $y; ?>年度</span>の日程・試合結果</h2></div></div>
		<?php
		foreach ( $docs as $doc ) {
			// 「区分 対象」ごとにまとめる
			$groups = array();
			foreach ( nfa_rows( $doc ) as $r ) {
				$key              = trim( $r['section'] . ( $r['target'] && $r['target'] !== $r['section'] ? ' ' . $r['target'] : '' ) );
				$groups[ $key ][] = $r;
			}
			if ( ! $groups ) {
				continue;
			}
			echo '<article class="arc-i"><h3>' . esc_html( get_the_title( $doc ) ) . '</h3><div class="tbl-w"><table class="tbl arc-t"><tbody>';
			foreach ( $groups as $key => $rows ) {
				echo '<tr><th scope="row">' . esc_html( '' !== $key ? $key : '大会資料' ) . '</th><td><div class="gap">' . implode( '', array_map( 'nfa_fbtn', $rows ) ) . '</div></td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</tbody></table></div></article>';
		}
		?>
</div></section>
	<?php endforeach; ?>
<?php else : ?>
<section class="blk"><div class="wrap"><p class="lead2">過去の年度の資料はまだありません。</p></div></section>
<?php endif; ?>
<section class="blk pale"><div class="wrap center"><a class="btn btn-n" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $ja ); ?>のページへ戻る<?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div></section>
<?php
get_footer();
