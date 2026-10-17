"""共通サムネイル・OGP・ファビコンを作る（文字はBarlow Condensedのアウトライン化、地紋は等高線）。"""
import numpy as np, os
from fontTools.ttLib import TTFont
from fontTools.pens.svgPathPen import SVGPathPen
from fontTools.pens.transformPen import TransformPen
import matplotlib; matplotlib.use('Agg')
import matplotlib.pyplot as plt
G = '/home/claude/web/gfx'; OUT = G + '/out'; os.makedirs(OUT, exist_ok=True)
NAVY, BLUE, CYAN, LCYAN = '#141C5E', '#1D2A8C', '#0090C8', '#7FD3FF'

def text_path(ttf, text, size, x, y, track=0.0, anchor='middle'):
    f = TTFont(ttf); gs = f.getGlyphSet(); cmap = f.getBestCmap(); upm = f['head'].unitsPerEm; hmtx = f['hmtx']
    s = size / upm; names = [cmap[ord(c)] for c in text]
    adv = [hmtx[n][0] * s for n in names]; w = sum(adv) + track * size * (len(text) - 1)
    cx = x - (w / 2 if anchor == 'middle' else 0)
    out = []
    for n, a in zip(names, adv):
        pen = SVGPathPen(gs); tp = TransformPen(pen, (s, 0, 0, -s, cx, y)); gs[n].draw(tp)
        out.append(pen.getCommands()); cx += a + track * size
    return out, w

def contours(w, h, step=120, cx=0.40, cy=0.52, zoom=1.5):
    """北アルプス（槍・穂高〜安曇野）の実際の標高から引いた等高線。step=等高線の間隔(m)"""
    from scipy.ndimage import gaussian_filter
    E = gaussian_filter(np.load('/home/claude/web/gfx/dem/alps.npy'), 4.5)
    H, W = E.shape; ar = w / h
    cw = int(min(W, H * ar) / zoom); ch = int(cw / ar)
    x0 = int(min(max(cx * W - cw / 2, 0), W - cw)); y0 = int(min(max(cy * H - ch / 2, 0), H - ch))
    sub = E[y0:y0 + ch, x0:x0 + cw]
    xs = np.linspace(0, w, sub.shape[1]); ys = np.linspace(0, h, sub.shape[0])
    lv = np.arange(np.ceil(sub.min() / step) * step, sub.max(), step)
    fig = plt.figure(); cs = plt.contour(xs, ys, sub, levels=lv); plt.close(fig)
    thin, bold = [], []
    for z, segs in zip(cs.levels, cs.allsegs):
        for seg in segs:
            if len(seg) < 6: continue
            d = 'M' + 'L'.join(f'{a:.1f},{b:.1f}' for a, b in seg)
            (bold if z % (step * 5) == 0 else thin).append(d)
    return ''.join(thin), ''.join(bold)

def card(w, h, inner, step=120, stroke=1.1, **kw):
    thin, bold = contours(w, h, step, **kw)
    return (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {w} {h}" width="{w}" height="{h}">'
            f'<defs><radialGradient id="g" cx="50%" cy="42%" r="75%"><stop offset="0" stop-color="{BLUE}"/><stop offset="1" stop-color="{NAVY}"/></radialGradient>'
            f'<filter id="n"><feTurbulence type="fractalNoise" baseFrequency=".9" numOctaves="2" seed="3"/><feColorMatrix values="0 0 0 0 1  0 0 0 0 1  0 0 0 0 1  0 0 0 .05 0"/></filter></defs>'
            f'<rect width="{w}" height="{h}" fill="url(#g)"/>'
            f'<path d="{thin}" fill="none" stroke="#fff" stroke-opacity=".07" stroke-width="{stroke}"/>'
            f'<path d="{bold}" fill="none" stroke="#fff" stroke-opacity=".14" stroke-width="{stroke*1.4}"/>'
            f'<rect width="{w}" height="{h}" filter="url(#n)"/>' + inner + '</svg>')

XB = G + '/font/BC-XB.ttf'; XBI = G + '/font/BC-XBI.ttf'

# 1) 画像のない投稿のサムネイル 1600×1000（16:10。1:1・4:3に切り抜かれても中央のNFAが残る大きさ）
p, _ = text_path(XB, 'NFA', 300, 800, 610, track=-0.035)
open(OUT + '/thumb-default.svg', 'w').write(card(1600, 1000, f'<path d="{"".join(p)}" fill="#fff"/>'))

# 2) OGP 1200×630：NFAを大きく、下にLife+1
p, _ = text_path(XB, 'NFA', 250, 600, 345, track=-0.035)
l1, w1 = text_path(XBI, 'Life', 74, 0, 0, anchor='start')
pl, wp = text_path(XBI, '+', 74, 0, 0, anchor='start')
o1, w3 = text_path(XBI, '1', 74, 0, 0, anchor='start')
tw = w1 + wp + w3 + 8; x0 = 600 - tw / 2; y = 478
L = (f'<g transform="translate({x0:.1f},{y})"><path d="{"".join(l1)}" fill="#fff"/>'
     f'<path transform="translate({w1+4:.1f},0)" d="{"".join(pl)}" fill="{LCYAN}"/>'
     f'<path transform="translate({w1+wp+8:.1f},0)" d="{"".join(o1)}" fill="#fff"/></g>')
open(OUT + '/ogp.svg', 'w').write(card(1200, 630, f'<path d="{"".join(p)}" fill="#fff"/>' + L))

# 3) ファビコン：青地に白のN
def fav(size, pad):
    p, _ = text_path(XB, 'N', size * (1 - pad), size / 2, size * 0.5 + size * (1 - pad) * 0.35)
    return (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {size} {size}" width="{size}" height="{size}">'
            f'<rect width="{size}" height="{size}" fill="{BLUE}"/><path d="{"".join(p)}" fill="#fff"/></svg>')
open(OUT + '/favicon.svg', 'w').write(fav(64, 0.12))
open(OUT + '/icon-512.svg', 'w').write(fav(512, 0.3))
print('ok', os.listdir(OUT))

# 4) PICK UP用（画像の上に日付・見出しが重なるカード）：NFAは右上に小さく
_, wN = text_path(XB, 'NFA', 170, 0, 0, track=-0.035, anchor='start')
p, _ = text_path(XB, 'NFA', 170, 1200 - 150 - wN, 90 + 170 * 0.7, track=-0.035, anchor='start')
open(OUT + '/thumb-default-pk.svg', 'w').write(card(1200, 900, f'<path d="{"".join(p)}" fill="#fff" fill-opacity=".92"/>'))
