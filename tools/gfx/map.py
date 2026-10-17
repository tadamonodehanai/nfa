"""長野県4地区のドット地図（SVG）を作る。境界：smartnews-smri/japan-topography（国土数値情報 N03 を簡素化）"""
import json, math
from shapely.geometry import shape, Point
from shapely.ops import unary_union, transform
from shapely import prepared
D = json.load(open('/home/claude/web/gfx/data/nagano.geojson'))
R = {
 '北信': '20201 20207 20211 20213 20218 20521 20541 20543 20561 20562 20563 20583 20588 20590 20602',
 '東信': '20203 20208 20217 20219 20303 20304 20305 20306 20307 20309 20321 20323 20324 20349 20350',
 '中信': '20202 20212 20215 20220 20422 20423 20425 20429 20430 20432 20446 20448 20450 20451 20452 20481 20482 20485 20486',
 '南信': '20204 20205 20206 20209 20210 20214 20361 20362 20363 20382 20383 20384 20385 20386 20388 20402 20403 20404 20407 20409 20410 20411 20412 20413 20414 20415 20416 20417',
}
code2r = {c: r for r, cs in R.items() for c in cs.split()}
assert len(code2r) == 77, len(code2r)
geo = {r: [] for r in R}
for f in D['features']:
    geo[code2r[f['properties']['N03_007']]].append(shape(f['geometry']).buffer(0))
k = math.cos(math.radians(36.1))
allg = unary_union([g for v in geo.values() for g in v])
minx, miny, maxx, maxy = allg.bounds
W = 400.0; S = W / ((maxx - minx) * k); H = (maxy - miny) * S
P = lambda x, y, z=None: ((x - minx) * k * S, (maxy - y) * S)
reg = {r: transform(P, unary_union(v)).buffer(0) for r, v in geo.items()}
pref = transform(P, allg).buffer(0)
def path(g):
    polys = [g] if g.geom_type == 'Polygon' else list(g.geoms)
    out = []
    for p in polys:
        for ring in [p.exterior] + list(p.interiors):
            cs = list(ring.coords)
            out.append('M' + 'L'.join(f'{x:.1f},{y:.1f}' for x, y in cs) + 'Z')
    return ''.join(out)
EN = {'北信': 'HOKUSHIN', '東信': 'TOSHIN', '中信': 'CHUSHIN', '南信': 'NANSHIN'}
step = 5.6; dots = {r: [] for r in R}; pr = {r: prepared.prep(g.buffer(-4.6)) for r, g in reg.items()}
y = step / 2; row = 0
while y < H:
    x = step / 2 + (step / 2 if row % 2 else 0)
    while x < W:
        pt = Point(x, y)
        for r in R:
            if pr[r].contains(pt): dots[r].append((x, y)); break
        x += step
    y += step * 0.866; row += 1
# 地区名の位置（地区の内側で最も余白のある点に近い位置）
lab = {r: g.representative_point() for r, g in reg.items()}
cen = {r: g.centroid for r, g in reg.items()}
pos = {r: (cen[r] if reg[r].contains(cen[r]) else lab[r]) for r in R}
nudge = {'北信': (0, 6), '東信': (-4, 0), '中信': (-6, 8), '南信': (-6, 8)}
g = []
for r in R:
    d = ''.join(f'M{x:.1f} {y:.1f}h0' for x, y in dots[r])
    px, py = pos[r].x + nudge[r][0], pos[r].y + nudge[r][1]
    g.append(f'<g class="mp-r" data-r="{r}" role="button" tabindex="0" aria-pressed="false" aria-label="{r}"><path class="mp-hit" d="{path(reg[r])}"/><path class="mp-dot" d="{d}"/>'
             f'<text class="mp-en" aria-hidden="true" x="{px:.0f}" y="{py-24:.0f}">{EN[r]}</text><text class="mp-ja" aria-hidden="true" x="{px:.0f}" y="{py+12:.0f}">{r}</text></g>')
borders = [reg[a].boundary.intersection(reg[b].boundary) for a in R for b in R if a < b]
def lpath(gm):
    ls = []
    for x in getattr(gm, 'geoms', [gm]):
        if x.geom_type in ('LineString', 'LinearRing'): ls.append('M' + 'L'.join(f'{a:.1f},{b:.1f}' for a, b in x.coords))
        elif hasattr(x, 'geoms'): ls += [lpath(y) for y in x.geoms]
    return ''.join(ls)
svg = (f'<svg class="mp" viewBox="0 0 {W:.0f} {H:.0f}" role="group" aria-label="長野県の4地区（北信・東信・中信・南信）">' + ''.join(g) + '</svg>')
open('/home/claude/web/gfx/map.svg', 'w').write(svg)
print(W, round(H), {r: len(v) for r, v in dots.items()}, len(svg))
