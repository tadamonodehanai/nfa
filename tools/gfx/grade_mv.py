"""MV 4枚を同じ明るさ・色調にそろえるグレーディング（元データ → src_assets/hero-*.jpg）"""
import numpy as np
from PIL import Image
from skimage import color
SRC='/home/claude/web/mv/lic/'; DST='/home/claude/web/build/src_assets/'
FILES=[('B_train','hero-train'),('A_match','hero-match'),('C_kids','hero-kids'),('D_land','hero-land'),('F_dev','fy-dev'),('F_start','fy-start'),('F_comm','fy-comm')]
WIDTH={'fy-dev':1600,'fy-start':1600,'fy-comm':1600}
T_L, T_SD, T_C = 48.0, 20.0, 21.0     # 目標：平均明度 / 明度のばらつき / 平均彩度（Lab）
def stats(lab): return lab[...,0].mean(), lab[...,0].std(), np.hypot(lab[...,1],lab[...,2]).mean(), lab[...,1].mean(), lab[...,2].mean()
def grade(name):
    im=Image.open(SRC+name+'.jpg').convert('RGB'); w=WIDTH.get(dict(FILES)[name],2400); im=im.resize((w,round(im.height*w/im.width)),Image.LANCZOS)
    rgb=np.asarray(im)/255.0; lab=color.rgb2lab(rgb)
    L,a,b=lab[...,0],lab[...,1],lab[...,2]
    before=stats(lab)
    # 1) 色かぶりを70%取り除く（画面全体の平均色をニュートラル寄りに）
    a=a-a.mean()*0.45; b=b-b.mean()*0.35
    # 2) 明度の平均とばらつきを目標にそろえる
    m,sd=L.mean(),max(L.std(),1e-3); L=(L-m)/sd*(sd+(T_SD-sd)*0.75)+(m+(T_L-m)*0.85)
    # 3) 共通のトーンカーブ：黒を少し持ち上げ、ハイライトはなだらかに（フィルム調）
    x=np.clip(L/100,0,1); x=0.035+0.93*(3*x**2-2*x**3)*0.45+0.93*x*0.55; L=np.clip(x*100,0,97)
    # 4) 彩度を共通の値にそろえ、強すぎる色（芝の緑・空の青）を抑える
    C=np.hypot(a,b); k=(C.mean()+(T_C-C.mean())*0.8)/max(C.mean(),1e-3); C2=C*k; C2=np.where(C2>40,40+(C2-40)*0.4,C2); s=np.where(C>1e-6,C2/np.maximum(C,1e-6),0); a,b=a*s,b*s
    # 5) 共通の色味：影をわずかに寒色、ハイライトをわずかに暖色（全写真に同じ量）
    t=np.clip(L/100,0,1); a=a+(t-0.5)*1.2; b=b+(t-0.5)*5.0
    out=color.lab2rgb(np.dstack([L,a,b])); out=np.clip(out,0,1)
    after=stats(color.rgb2lab(out))
    Image.fromarray((out*255+0.5).astype(np.uint8)).save(DST+name.replace(name,dict(FILES)[name])+'.jpg',quality=80,optimize=True,progressive=True)
    return before,after
for s,d in FILES:
    b,a=grade(s); print(d,'before L%.0f sd%.0f C%.0f a%.1f b%.1f'%b,'| after L%.0f sd%.0f C%.0f a%.1f b%.1f'%a)
