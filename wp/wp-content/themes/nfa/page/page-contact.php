<?php
/**
Template Name: CONTACT FORM

 * <meta content="charset=UTF-8">
 * @package nfa
 */

$url=get_bloginfo('url');
$assets_url=get_assets_url();
$mypageinfo=get_mypageinfo();

get_header();
while ( have_posts() ) : the_post();?>
<div class="stage">
<!-- ============ お問い合わせ ============ -->
  <div class="view" id="vForm">
    <div class="ctHead">
      <p class="crumb"><a href="<?= getPageUrl('home');?>">ホーム</a> ／ お問い合わせ</p>
      <h1>お問い合わせ</h1>
      <p>大会・登録・講習会など、用件によって回答できる部署が異なります。用件の種類をお選びいただくと、担当へ直接届きます。</p>
    </div>

    <div class="ctCols">
      <main>
        <?php get_template_part('parts/step','contact',array('step'=>1));?>

        <!-- 入力 -->
        <div id="fInput">
          <div class="fRow">
            <label>用件の種類<span class="req">必須</span></label>
            <select id="inKind">
              <option value="">お選びください</option>
              <option>大会・試合について</option>
              <option>チーム・選手の登録について</option>
              <option>審判・指導者の資格、講習会について</option>
              <option>協賛・パートナーシップについて</option>
              <option>取材・広報について</option>
              <option>その他</option>
            </select>
            <p class="hint">選択された種類に応じて、担当部署へ振り分けられます。</p>
          </div>
          <div class="fRow">
            <label>お名前<span class="req">必須</span></label>
            <input type="text" id="inName" placeholder="例）長野 太郎">
          </div>
          <div class="fRow">
            <label>ご所属<span class="opt2">任意</span></label>
            <input type="text" id="inOrg" placeholder="例）◯◯FC／◯◯株式会社（チーム・会社・学校など）">
          </div>
          <div class="fRow">
            <label>メールアドレス<span class="req">必須</span></label>
            <input type="email" id="inMail" placeholder="例）example@example.com">
            <p class="hint">ご返信はこちらのアドレスに差し上げます。お電話での回答をご希望の場合は、お問い合わせ内容にその旨と番号をご記入ください。</p>
          </div>
          <div class="fRow">
            <label>お問い合わせ内容<span class="req">必須</span></label>
            <textarea id="inBody" placeholder="できるだけ具体的にご記入ください。大会に関するお問い合わせは、大会名と年代（4種・3種など）を添えていただくと回答が早くなります。"></textarea>
          </div>

          <div class="agree">
            <p>ご記入いただいた個人情報は、お問い合わせへの回答のためにのみ使用します。くわしくは<a href="<?= getPageUrl('privacy');?>">プライバシーポリシー</a>をご確認ください。</p>
            <button class="chk" id="agreeChk"><span class="box"></span>同意します</button>
          </div>

          <div class="fActs">
            <button class="btnMain" id="toCfm" disabled>入力内容を確認する</button>
          </div>
        </div>

        <!-- 確認 -->
        <div id="fCfm" style="display:none">
          <div class="cfm">
            <dl>
              <dt>用件の種類</dt><dd id="cKind">─</dd>
              <dt>お名前</dt><dd id="cName">─</dd>
              <dt>ご所属</dt><dd id="cOrg">─</dd>
              <dt>メールアドレス</dt><dd id="cMail">─</dd>
              <dt>お問い合わせ内容</dt><dd id="cBody" style="white-space:pre-wrap">─</dd>
            </dl>
          </div>
          <div class="fActs">
            <button class="btnBack" id="backIn">入力に戻る</button>
            <button class="btnMain" id="toDone">この内容で送信する</button>
          </div>
		  </div>
      </main>

      <?php get_template_part('parts/aside','contact');?>
    </div>

  </div>
	
</div>
<script>
const $=s=>document.querySelector(s);

/* フォーム：同意チェックと遷移 */
let agreed=false;
$('#agreeChk').onclick=function(){
  agreed=!agreed;
  this.classList.toggle('on',agreed);
  validate();
};
['inKind','inName','inMail','inBody'].forEach(id=>{
  $('#'+id).addEventListener('input',validate);
  $('#'+id).addEventListener('change',validate);
});
function validate(){
  const ok=agreed && $('#inKind').value && $('#inName').value.trim() && $('#inMail').value.trim() && $('#inBody').value.trim();
  $('#toCfm').disabled=!ok;
}
$('#toCfm').onclick=()=>{
  $('#cKind').textContent=$('#inKind').value;
  $('#cName').textContent=$('#inName').value;
  $('#cOrg').textContent=$('#inOrg').value||'（未記入）';
  $('#cMail').textContent=$('#inMail').value;
  $('#cBody').textContent=$('#inBody').value;
  $('#fInput').style.display='none';
  $('#fCfm').style.display='block';
  $('#st1').classList.remove('on');$('#st2').classList.add('on');
  window.scrollTo({top:0,behavior:'smooth'});
};
$('#backIn').onclick=()=>{
  $('#fCfm').style.display='none';
  $('#fInput').style.display='block';
  $('#st2').classList.remove('on');$('#st1').classList.add('on');
};
	
	/*Contact Form 7で代用
$('#toDone').onclick=()=>{
  $('#fCfm').style.display='none';
  $('#fDone').style.display='block';
  $('#st2').classList.remove('on');$('#st3').classList.add('on');
	
  window.scrollTo({top:0,behavior:'smooth'});
  */
};
</script>
<?php endwhile;
get_footer();?>
