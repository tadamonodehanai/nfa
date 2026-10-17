"""協賛ロゴを、枠線と余白を取り除いて同じ規格のPNG（透過）にそろえる"""
import numpy as np
from PIL import Image, ImageOps
R='/home/claude/web/sp/raw/'; O='/home/claude/web/sp/off/'; OUT='/home/claude/web/build/src_assets/'
SRC={'maxell':R+'maxell.png','alpico':R+'alpico.png','tsb':O+'tsb_foot.jpg','adbureau':R+'adbureau.gif','richade':R+'richade.png',
     'abn':R+'abn.png','tutti':R+'tutti.png','mizuho':R+'mizuho.png','meiji':R+'meiji.png','adidas':R+'adidas.jpg','real':R+'real.png'}
def prep(k,p):
    im=Image.open(p).convert('RGB')
    if k=='adidas': im=ImageOps.invert(im)          # 黒地に白 → 白地に黒（標準の配色）
    a=np.asarray(im).astype(int)
    # 外周の細い枠線（灰色1〜3px）を落とす
    a=a[3:-3,3:-3]
    if k=='meiji':                                     # 色面のロゴはそのまま（外枠だけ除去）
        rgba=np.dstack([a,np.full(a.shape[:2],255)])
    else:
        # 白に近いほど透明に（アンチエイリアスを保つ）
        d=255-a.min(axis=2); alpha=np.clip(d*255/40,0,255)
        rgba=np.dstack([a,alpha])
    im=Image.fromarray(rgba.astype(np.uint8),'RGBA')
    bb=im.getchannel('A').point(lambda v:255 if v>24 else 0).getbbox(); im=im.crop(bb)
    # 高さ200pxにそろえて書き出し（拡大は2倍まで）
    h=min(200,im.height*2); im=im.resize((round(im.width*h/im.height),h),Image.LANCZOS)
    im.save(OUT+f'sp-{k}.png',optimize=True); return im.size
for k,p in SRC.items(): print(k,prep(k,p))
