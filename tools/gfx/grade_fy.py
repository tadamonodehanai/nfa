"""FOR YOU 3枚を、カードに映る範囲（16:9の中央）の統計で厳密にそろえる"""
import numpy as np
from PIL import Image
from skimage import color
SRC='/home/claude/web/mv/lic/'; OUT='/home/claude/web/build/src_assets/'
F=[('F_dev','fy-dev'),('F_start','fy-start'),('F_comm','fy-comm')]
T=dict(L=47.0, sd=19.0, a=-1.0, b=4.0, C=17.0)   # 目標：明度・ばらつき・色味（わずかに暖色）・彩度
def crop169(lab):
    h,w=lab.shape[:2]; ch=int(w*9/16); 
    if ch>h: cw=int(h*16/9); x=(w-cw)//2; return lab[:,x:x+cw]
    y=(h-ch)//2; return lab[y:y+ch]
for s,d in F:
    im=Image.open(SRC+s+'.jpg').convert('RGB'); im=im.resize((1600,round(im.height*1600/im.width)),Image.LANCZOS)
    lab=color.rgb2lab(np.asarray(im)/255.0); L,a,b=lab[...,0],lab[...,1],lab[...,2]
    v=crop169(lab); vL,va,vb=v[...,0],v[...,1],v[...,2]
    # 明度：平均とばらつきを一致
    L=(L-vL.mean())/max(vL.std(),1e-3)*T['sd']+T['L']
    x=np.clip(L/100,0,1); x=0.03+0.94*((3*x**2-2*x**3)*0.4+x*0.6); L=np.clip(x*100,1,97)
    # 色味：平均の色かぶりを完全に取り除いてから、共通の色味を足す
    a=a-va.mean(); b=b-vb.mean()
    C=np.hypot(a,b); vC=np.hypot(va-va.mean(),vb-vb.mean()).mean()
    k=T['C']/max(vC,1e-3); C2=C*k; C2=np.where(C2>36,36+(C2-36)*0.35,C2); s_=np.where(C>1e-6,C2/np.maximum(C,1e-6),0); a,b=a*s_,b*s_
    t=np.clip(L/100,0,1); a=a+T['a']+(t-0.5)*1.0; b=b+T['b']+(t-0.5)*6.0
    out=np.clip(color.lab2rgb(np.dstack([L,a,b])),0,1)
    o=(out*255+0.5).astype(np.uint8); Image.fromarray(o).save(OUT+d+'.jpg',quality=80,optimize=True,progressive=True)
    r=crop169(color.rgb2lab(out)); print(d,'L%.1f sd%.1f a%.1f b%.1f C%.1f'%(r[...,0].mean(),r[...,0].std(),r[...,1].mean(),r[...,2].mean(),np.hypot(r[...,1],r[...,2]).mean()))
