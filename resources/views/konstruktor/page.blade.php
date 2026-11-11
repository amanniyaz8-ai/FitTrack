@verbatim
<!doctype html>
<html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>Конструктор тренировок и КБЖУ</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Unbounded:wght@500;600;700;800&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap">
<style>
/* Layout: one column like a gym logbook — numbered chapters (a real sequence) split by a rule and a big step number; plate colours mark exercise importance */
:root{
  --bg:#F1F3F6; --surface:#FFFFFF; --sunk:#E9ECF1; --fg:#1A1F29; --muted:#5B6474; --line:#D3D8E1;
  --accent:#1E4FD8; --accent-fg:#FFFFFF; --accent-soft:#E3EAFC;
  --plate-red:#D33B2C; --plate-blue:#1E4FD8; --plate-yellow:#E7B416; --plate-green:#2E9A55;
  --good:#1E7B3A; --good-bg:#E2F3E7; --bad:#B3261E; --bad-bg:#FBE6E4; --warn-bg:#FFF4D6; --warn-line:#E7B416; --input:#FFFBEA;
  --f-display:"Unbounded","Arial Black",Arial,sans-serif;
  --f-body:"Manrope",system-ui,-apple-system,"Segoe UI",Arial,sans-serif;
  --f-data:"JetBrains Mono",ui-monospace,Menlo,Consolas,monospace;
}
@media (prefers-color-scheme: dark){:root:not([data-theme="light"]){
  --bg:#12151B; --surface:#1B2029; --sunk:#232A35; --fg:#E8ECF2; --muted:#9AA4B5; --line:#2F3744;
  --accent:#5B86FF; --accent-fg:#0B0F16; --accent-soft:#1F2B4A; --plate-yellow:#F0C43A; --plate-blue:#5B86FF;
  --good:#6FD08F; --good-bg:#183224; --bad:#FF8A80; --bad-bg:#3A1C1A; --warn-bg:#332B12; --warn-line:#F0C43A; --input:#262A20; color-scheme:dark}}
:root[data-theme="dark"]{
  --bg:#12151B; --surface:#1B2029; --sunk:#232A35; --fg:#E8ECF2; --muted:#9AA4B5; --line:#2F3744;
  --accent:#5B86FF; --accent-fg:#0B0F16; --accent-soft:#1F2B4A; --plate-yellow:#F0C43A; --plate-blue:#5B86FF;
  --good:#6FD08F; --good-bg:#183224; --bad:#FF8A80; --bad-bg:#3A1C1A; --warn-bg:#332B12; --warn-line:#F0C43A; --input:#262A20; color-scheme:dark}
*{box-sizing:border-box}
body{background:var(--bg);color:var(--fg);font-family:var(--f-body);font-size:15px;line-height:1.5}
.wrap{max-width:920px;margin:0 auto;padding-block:32px 72px;padding-left:max(16px,env(safe-area-inset-left,0px));padding-right:max(16px,env(safe-area-inset-right,0px));display:grid;gap:44px}
h1,h2,h3{font-family:var(--f-display);font-weight:600;letter-spacing:.01em;text-wrap:balance;margin:0;line-height:1.15}
h1{font-size:clamp(28px,6.2vw,48px);font-weight:800;letter-spacing:-.01em;line-height:1.08}
h1 em{font-style:normal;color:var(--accent)}
.hero{display:grid;gap:12px;padding-block:8px 4px}
.eyebrow{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--accent)}
h2{font-size:clamp(20px,4.6vw,26px);font-weight:700;text-transform:none;letter-spacing:-.01em}
h3{font-weight:600}
section{padding-top:36px;border-top:2px solid var(--fg)}
section>h2{gap:14px}
section>h2 .step{display:inline-block;flex:none;width:auto;height:auto;border-radius:0;background:none;color:var(--accent);font-family:var(--f-display);font-size:clamp(40px,9vw,60px);font-weight:800;line-height:.9;letter-spacing:-.03em}
section>h2 .step::before{content:"0"}
.panel.form>*+*{border-top:1px dashed var(--line);padding-top:16px}
.panel{gap:20px}
h2{font-size:24px;text-transform:uppercase;display:flex;gap:12px;align-items:center}
h3{font-size:18px}
p{margin:0}
.lead{color:var(--muted);max-width:62ch;font-size:16px}
.step{display:inline-grid;place-items:center;width:34px;height:34px;border-radius:50%;background:var(--fg);color:var(--bg);font-family:var(--f-data);font-size:15px;flex:none}
section{display:grid;gap:16px}
.panel{background:var(--surface);border:1px solid var(--line);border-radius:14px;padding:20px;display:grid;gap:16px;min-width:0}
.hint{color:var(--muted);font-size:13px}
.grid{display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(170px,1fr))}
label.f{display:grid;gap:6px;font-weight:600;font-size:14px}
label.f small{font-weight:400;color:var(--muted)}
input,select{font:inherit;color:var(--fg);background:var(--input);border:1px solid var(--line);border-radius:9px;padding:10px 12px;width:100%;min-width:0}
select{background:var(--surface)}
input:focus-visible,select:focus-visible,button:focus-visible{outline:3px solid var(--accent);outline-offset:2px}
.choices{display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr))}
.choice{all:unset;cursor:pointer;display:grid;gap:6px;padding:16px;border-radius:12px;border:2px solid var(--line);background:var(--surface);color:var(--fg)}
.choice b{font-family:var(--f-display);font-size:16px;font-weight:700}
.choice span{color:var(--muted);font-size:14px}
.choice[aria-pressed="true"]{border-color:var(--accent);box-shadow:inset 0 0 0 1px var(--accent)}
.choice:focus-visible{outline:3px solid var(--accent);outline-offset:2px}
.seg{display:flex;flex-wrap:wrap;gap:8px}
.seg button{font:inherit;font-weight:600;cursor:pointer;border:1px solid var(--line);background:var(--surface);color:var(--fg);border-radius:999px;padding:8px 16px}
.seg button[aria-pressed="true"]{background:var(--fg);color:var(--bg);border-color:var(--fg)}
.daypick{display:flex;flex-wrap:wrap;gap:8px}
.daypick button{font:inherit;font-weight:700;cursor:pointer;width:52px;height:52px;border-radius:12px;border:2px solid var(--line);background:var(--surface);color:var(--fg)}
.daypick button[aria-pressed="true"]{background:var(--accent);border-color:var(--accent);color:var(--accent-fg)}
.kcal{display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(180px,1fr))}
.stat{background:var(--sunk);border-radius:12px;padding:14px 16px;display:grid;gap:2px}
.stat .lab{font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);font-weight:700}
.stat .val{font-family:var(--f-data);font-size:28px;font-weight:700;font-variant-numeric:tabular-nums}
.stat .sub{font-size:13px;color:var(--muted)}
.stat.main{background:var(--fg);color:var(--bg)}
.stat.main .lab,.stat.main .sub{color:var(--bg);opacity:.75}
.macro-bar{display:flex;height:14px;border-radius:7px;overflow:hidden}
.macro-bar i{display:block}
.legend{display:flex;flex-wrap:wrap;gap:14px;font-size:13px;color:var(--muted)}
.dot{display:inline-block;width:10px;height:10px;border-radius:3px;margin-right:6px;vertical-align:middle}
.note{border-left:3px solid var(--plate-yellow);background:var(--sunk);padding:10px 14px;border-radius:0 10px 10px 0;font-size:14px}
/* 12-week map */
.weekmap{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
.blk{display:grid;gap:6px;min-width:0}
.blk .bt{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted)}
.blk .bt b{color:var(--fg)}
.wk{display:grid;grid-template-columns:repeat(4,1fr);gap:4px}
.wk button{font:inherit;cursor:pointer;border:1px solid var(--line);background:var(--surface);color:var(--fg);border-radius:8px;padding:8px 0;font-family:var(--f-data);font-weight:700;font-size:14px}
.wk button.dl{background:var(--sunk);color:var(--muted)}
.wk button[aria-pressed="true"]{background:var(--accent);border-color:var(--accent);color:var(--accent-fg)}
@media (max-width:560px){.weekmap{grid-template-columns:1fr}}
.weekinfo{display:grid;gap:4px;padding:14px 16px;border-radius:12px;background:var(--accent-soft)}
.weekinfo b{font-family:var(--f-display);font-size:15px;font-weight:700;line-height:1.35}
.navrow{display:flex;justify-content:space-between;gap:10px}
/* workout */
.panel.day{display:grid;gap:0;padding:0;overflow:hidden}
.dayhead{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:8px;padding:16px 20px;background:var(--fg);color:var(--bg)}
.dayhead h3{font-size:19px;font-weight:700}
.dayhead span{font-family:var(--f-data);font-size:14px;opacity:.85}
.warm{display:grid;gap:10px;padding:16px 20px;background:var(--warn-bg);border-bottom:1px solid var(--line)}
.warm h4{margin:0;font-family:var(--f-display);font-weight:700;font-size:13px;text-transform:uppercase;letter-spacing:.04em}
.warm ol{margin:0;padding:0;list-style:none;display:grid;gap:6px;counter-reset:w}
.warm li{display:grid;grid-template-columns:24px 1fr;gap:8px;font-size:14px;counter-increment:w}
.warm li::before{content:counter(w);font-family:var(--f-data);font-weight:800;width:22px;height:22px;border-radius:6px;display:grid;place-items:center;background:var(--warn-line);color:#1A1F29;font-size:12px}
.warm li b{font-weight:700}
.exlist{display:grid;padding:4px 20px 0}
.ex{display:grid;grid-template-columns:44px minmax(0,1fr) auto;gap:6px 14px;padding:16px 0;border-bottom:1px solid var(--line);align-items:start}
.plate{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;font-family:var(--f-data);font-weight:800;font-size:13px;color:#fff;border:3px solid rgba(0,0,0,.18)}
.plate.y{color:#1A1F29}
.exname{font-weight:800;font-size:17px;line-height:1.25}
.exmeta{display:flex;flex-wrap:wrap;gap:4px 14px;font-size:13px;color:var(--muted);margin-top:4px}
.exmeta b{color:var(--fg);font-weight:700}
.scheme{display:flex;align-items:stretch;border:2px solid var(--fg);border-radius:12px;overflow:hidden;font-family:var(--f-data);min-width:0}
.scheme div{display:grid;place-items:center;padding:6px 12px;min-width:64px;text-align:center}
.scheme b{font-size:24px;font-weight:800;line-height:1.1;font-variant-numeric:tabular-nums;white-space:nowrap}
.scheme span{font-family:var(--f-body);font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;font-weight:700}
.scheme i{display:grid;place-items:center;font-style:normal;font-size:20px;font-weight:800;background:var(--fg);color:var(--bg);padding:0 8px}
.scheme.t b{font-size:18px}
.pair{grid-column:2/4;font-size:12px;font-weight:700;color:var(--accent);background:var(--accent-soft);border-radius:8px;padding:4px 10px;justify-self:start}
.sets{grid-column:2/4;display:flex;flex-wrap:wrap;gap:8px;margin-top:4px}
.set{display:grid;grid-template-columns:auto 1fr auto 1fr;align-items:center;gap:4px;padding:6px 8px;border:1px solid var(--line);border-radius:10px;background:var(--surface);width:172px}
.set .n{font-family:var(--f-data);font-size:11px;font-weight:800;color:var(--muted);width:18px}
.set input{padding:6px 2px;font-family:var(--f-data);font-size:15px;text-align:center;border-radius:7px}
.set em{font-style:normal;color:var(--muted);font-size:13px}
.set.done{border-color:var(--good);background:var(--good-bg)}
.exfoot{grid-column:2/4;display:flex;flex-wrap:wrap;gap:8px 14px;align-items:center;font-size:13px}
.ton{font-family:var(--f-data);font-weight:700}
.check{display:flex;align-items:center;gap:8px;font-weight:600;font-size:14px}
.check input{width:22px;height:22px;accent-color:var(--good)}
.tip{font-size:13px;font-weight:700;padding:4px 10px;border-radius:999px}
.tip.up{background:var(--good-bg);color:var(--good)}
.tip.down{background:var(--bad-bg);color:var(--bad)}
.tip.same{background:var(--sunk);color:var(--fg)}
.daytot{display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;padding:14px 20px;font-size:13px;color:var(--muted)}
.daytot b{font-family:var(--f-data);color:var(--fg);font-size:16px}
@media (max-width:560px){
  .ex{grid-template-columns:40px minmax(0,1fr)}
  .scheme{grid-column:2/3;justify-self:start}
  .pair,.sets,.exfoot{grid-column:1/3}
  .set{width:calc(50% - 4px)}
  .exlist,.warm,.dayhead,.daytot{padding-left:16px;padding-right:16px}
}
.bars{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:4px;align-items:end;height:130px}
.bar{display:grid;gap:4px;align-items:end;height:100%;grid-template-rows:1fr auto;text-align:center;min-width:0}
.bar i{display:block;background:var(--accent);border-radius:4px 4px 0 0;min-height:2px;align-self:end}
.bar.dl i{background:var(--muted)}
.bar.cur i{background:var(--plate-red)}
.bar span{font-size:11px;color:var(--muted);font-family:var(--f-data)}
.timeline{display:grid;gap:10px}
.tl{display:grid;grid-template-columns:auto 1fr;gap:4px 14px;padding:14px;border-radius:12px;border:1px solid var(--line);background:var(--surface)}
.tl.cur{border-color:var(--accent);box-shadow:inset 4px 0 0 var(--accent)}
.tl .w{font-family:var(--f-data);font-size:13px;color:var(--muted);grid-row:span 2;min-width:78px}
.tl h3{font-size:17px}
.tl p{font-size:14px;color:var(--muted)}
.rules{display:grid;gap:8px;padding-left:20px;margin:0}
.btn{font:inherit;font-weight:700;cursor:pointer;border:0;background:var(--accent);color:var(--accent-fg);border-radius:10px;padding:10px 16px}
.btn.ghost{background:transparent;color:var(--fg);border:1px solid var(--line)}
.btn:disabled{opacity:.4;cursor:default}
.row{display:flex;flex-wrap:wrap;gap:10px;align-items:center}
.example{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;background:var(--plate-yellow);color:#1A1F29;border-radius:6px;padding:2px 8px}
dl.terms{display:grid;grid-template-columns:minmax(90px,max-content) 1fr;gap:8px 16px;margin:0;font-size:14px}
dl.terms dt{font-weight:700}
dl.terms dd{margin:0;color:var(--muted)}
#copyArea{width:100%;height:120px;font-family:var(--f-data);font-size:12px}
.alarm{border:2px solid var(--bad);background:var(--bad-bg);border-radius:14px;padding:18px 20px;display:grid;gap:8px}
.alarm h3{color:var(--bad);text-transform:uppercase}
.alarm ul{margin:0;padding-left:20px;display:grid;gap:4px}
.cycle{display:grid;gap:10px;padding:16px;border-radius:12px;background:var(--sunk)}
.cycle b.big{font-family:var(--f-display);font-size:17px;font-weight:700}
.cyc{display:grid;gap:12px;padding:14px 16px;border-radius:12px;background:var(--sunk)}
.phase{display:grid;gap:4px;padding:12px 20px;border-left:6px solid var(--pc);background:var(--surface);border-bottom:1px solid var(--line)}
.pchip{font-weight:800;font-size:13px;text-transform:uppercase;letter-spacing:.04em}
.phase p{font-size:14px;color:var(--muted)}
.cycinfo{display:grid;gap:8px;padding:16px;border-radius:12px;border:1px solid var(--line)}
.cycinfo .big{font-family:var(--f-display);font-size:16px;font-weight:700}
.tablewrap{display:grid;gap:8px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr))}
.pcard{display:grid;gap:4px;padding:12px 14px;border-radius:10px;background:var(--sunk);border-left:5px solid var(--pc);font-size:14px;min-width:0}
.pcard .ph1{display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap}
.pcard .ph1 span{font-family:var(--f-data);font-size:13px;color:var(--muted)}
.pcard i{font-style:normal;color:var(--muted)}
.tablewrap table{width:100%;border-collapse:collapse;font-size:13px;min-width:560px}
.tablewrap th,.tablewrap td{text-align:left;padding:8px 10px;border-bottom:1px solid var(--line);vertical-align:top}
.tablewrap th{font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--muted)}
.tablewrap td.num{font-family:var(--f-data);white-space:nowrap}
.rule{border-color:var(--plate-yellow);border-width:2px}
.split{display:grid;gap:10px;padding:14px 16px;border-radius:12px;border:1px solid var(--line)}
.split .eq{display:flex;flex-wrap:wrap;align-items:center;gap:8px;font-family:var(--f-data);font-size:15px}
.split .eq span{padding:6px 10px;border-radius:8px;background:var(--sunk)}
.split .eq b{font-weight:800}
.split .warnline{color:var(--bad);font-weight:700;font-size:14px}
.inj{display:grid;gap:12px;padding:16px;border-radius:12px;border:2px solid var(--plate-blue)}
.inj .big{font-family:var(--f-display);font-size:16px;font-weight:700}
.inj .alert{background:var(--bad-bg);color:var(--bad);font-weight:700;padding:10px 12px;border-radius:10px;font-size:14px}
.injgrid{display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr))}
.injgrid>div{min-width:0}
.injgrid h4{margin:0 0 6px;font-family:var(--f-display);font-weight:700;font-size:13px;text-transform:uppercase;letter-spacing:.04em}
.injgrid ul,.swaps{margin:0 0 12px;padding-left:18px;display:grid;gap:4px;font-size:14px}
.injgrid ul.red li::marker{color:var(--bad)}
.swaps{margin-top:8px;color:var(--muted)}
.swaps b{color:var(--fg)}
.iq{display:grid;gap:10px;padding:14px 16px;border-radius:12px;border:2px solid var(--plate-blue);background:var(--surface)}
.iqt{font-family:var(--f-display);font-size:15px;font-weight:700}
.iqg{display:grid;gap:10px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr))}
.iqc{border-radius:10px;padding:10px 12px;background:var(--sunk);min-width:0}
.iqc h4{margin:0 0 6px;font-family:var(--f-display);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.04em}
.iqc ul{margin:0;padding-left:18px;display:grid;gap:4px;font-size:13px}
.iqc.ok{border-left:4px solid var(--good)}.iqc.ok h4{color:var(--good)}
.iqc.no{border-left:4px solid var(--bad)}.iqc.no h4{color:var(--bad)}
.iqc.sw{border-left:4px solid var(--plate-blue)}
.iqc s{color:var(--muted)}
.swapnote{grid-column:2/4;font-size:12px;color:var(--muted)}
.swapnote b{color:var(--plate-blue)}
@media (max-width:520px){.panel{padding:16px}.stat .val{font-size:24px}}
@media (prefers-reduced-motion:no-preference){.bar i{transition:height .3s}}

.ob{position:fixed;inset:0;z-index:50;background:rgba(10,13,20,.55);display:grid;place-items:center;padding:16px}
.ob[hidden]{display:none}
.ob-card{width:min(520px,100%);max-height:calc(100dvh - 32px);overflow:auto;background:var(--surface);color:var(--fg);border-radius:20px;padding:20px 20px 14px;display:grid;gap:16px;box-shadow:0 20px 60px rgba(0,0,0,.3)}
.ob-prog{height:6px;border-radius:6px;background:var(--sunk);overflow:hidden}.ob-prog i{display:block;height:100%;background:var(--accent);transition:width .25s}
.ob-body{display:grid;gap:14px;min-height:220px;align-content:start}
.ob-q{font-family:var(--f-display);font-size:22px;line-height:1.25;margin:4px 0 0}
.ob-in{width:100%;box-sizing:border-box;font:600 22px var(--f-body);padding:14px 16px;border:2px solid var(--line);border-radius:14px;background:var(--bg);color:var(--fg)}
.ob-in:focus{outline:none;border-color:var(--accent)}
.ob-num{display:flex;align-items:center;gap:10px}.ob-num span{font-weight:700;color:var(--muted);min-width:70px}
.ob-opts{display:grid;gap:10px}
.ob-opt{text-align:left;display:grid;gap:3px;padding:14px 16px;border-radius:14px;border:2px solid var(--line);background:var(--bg);color:var(--fg);font:inherit;cursor:pointer}
.ob-opt b{font-size:17px}.ob-opt span{font-size:13px;color:var(--muted)}
.ob-opt[aria-pressed="true"]{border-color:var(--accent);background:var(--accent-soft)}
.ob-days{display:grid;grid-template-columns:repeat(7,1fr);gap:6px}
.ob-day{padding:14px 0;border-radius:12px;border:2px solid var(--line);background:var(--bg);color:var(--fg);font:700 15px var(--f-body);cursor:pointer}
.ob-day[aria-pressed="true"]{border-color:var(--accent);background:var(--accent);color:var(--accent-fg)}
.ob-nav{display:flex;justify-content:space-between;gap:10px}
.ob-back,.ob-next{font:700 16px var(--f-body);padding:14px 20px;border-radius:14px;border:0;cursor:pointer}
.ob-back{background:transparent;color:var(--muted)}.ob-next{background:var(--accent);color:var(--accent-fg);flex:1}
.ob-skip{background:none;border:0;color:var(--muted);font:13px var(--f-body);text-decoration:underline;cursor:pointer;justify-self:center}

.ob-hero{display:flex;align-items:center;gap:12px;min-height:124px}
.kb{width:124px;height:124px;flex:none;animation:kbIdle 2.4s ease-in-out infinite;transform-origin:50% 90%}
.kb .kb-eyes{animation:kbBlink 4s infinite;transform-origin:70px 95px}
.kb.jump{animation:kbJump .5s ease-out}
.kb.flex{animation:kbJump .5s ease-out}.kb.flex .kb-arm-l{animation:kbFlexL .9s ease-in-out 2;transform-origin:34px 86px}.kb.flex .kb-arm-r{animation:kbFlexR .9s ease-in-out 2;transform-origin:106px 86px}
.ob-say{position:relative;background:var(--accent-soft);color:var(--fg);font-weight:600;font-size:15px;padding:10px 14px;border-radius:14px;line-height:1.35}
.ob-say:before{content:"";position:absolute;left:-7px;top:50%;margin-top:-7px;border:7px solid transparent;border-right-color:var(--accent-soft);border-left:0}
.ob-say:empty{visibility:hidden}
@keyframes kbIdle{0%,100%{transform:translateY(0) rotate(0)}50%{transform:translateY(-4px) rotate(-2deg)}}
@keyframes kbBlink{0%,92%,100%{transform:scaleY(1)}95%{transform:scaleY(.1)}}
@keyframes kbJump{0%{transform:translateY(0) scale(1,1)}25%{transform:translateY(0) scale(1.1,.88)}55%{transform:translateY(-22px) scale(.94,1.06)}80%{transform:translateY(0) scale(1.06,.94)}100%{transform:translateY(0) scale(1,1)}}
@keyframes kbFlexL{0%,100%{transform:rotate(0)}50%{transform:rotate(25deg)}}
@keyframes kbFlexR{0%,100%{transform:rotate(0)}50%{transform:rotate(-25deg)}}
@media (prefers-reduced-motion:reduce){.kb,.kb *{animation:none!important}}
</style>

<style>body{margin:0;font-size:14px}[hidden]{display:none!important}img{max-width:100%}</style>
</head><body>
@endverbatim
<script src="https://telegram.org/js/telegram-web-app.js"></script>
<script>window.TP_API = {!! json_encode(['state' => $student->state, 'saveUrl' => route('konstruktor.state'), 'browserLink' => route('konstruktor.browserLink'), 'sendFile' => route('konstruktor.sendFile'), 'csrf' => csrf_token()], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!};</script>
<form method="POST" action="{{ route('konstruktor.logout') }}" style="position:fixed;right:max(12px,env(safe-area-inset-right,0px));bottom:max(12px,env(safe-area-inset-bottom,0px));z-index:20;margin:0">
    @csrf
    <button type="submit" style="font:600 12px system-ui,sans-serif;padding:6px 12px;border-radius:999px;border:1px solid #D3D8E1;background:#fff;color:#5B6474;cursor:pointer">Выйти{{ $student->first_name ? ' · '.e($student->first_name) : '' }}</button>
</form>
@verbatim

<div class="wrap">
  <header class="hero">
    <p class="hint" id="syncMsg" style="justify-self:end"></p>
    <span class="eyebrow">Тренировки · питание · 12 недель</span>
    <h1>Конструктор тренировок <em>и КБЖУ</em></h1>
    <p class="lead">Заполни 3 шага — получишь программу тренировок на 12 недель, свои калории и понятный план, как расти. Ты вписываешь только вес и повторы на тренировке, остальное считается само. Всё сохраняется в этом браузере.</p>
    <p><button type="button" class="btn ghost" id="obAgain">Пройти анкету заново</button></p>
    <div class="panel" id="dlTop" hidden style="border:2px solid var(--accent);display:grid;gap:12px">
      <b style="font-size:20px">Твоя программа готова — скачай её</b>
      <div class="row"><button type="button" class="btn" id="dlTopHtml">Скачать программу (для печати)</button><button type="button" class="btn ghost" id="dlTopCsv">Скачать таблицу (Excel)</button></div>
      <p class="hint" id="dlTopMsg"></p>
    </div>
  </header>

  <section aria-labelledby="s1">
    <h2 id="s1"><span class="step">1</span>О тебе</h2>
    <div class="panel form">
      <div class="row"><span class="example" id="exTag">Пример — впиши свои данные</span></div>
      <div class="grid">
        <label class="f">Имя<input id="name" type="text" value="Айдос"></label>
        <label class="f">Пол<select id="sex"><option value="m">Мужской</option><option value="f">Женский</option></select></label>
        <label class="f">Возраст, лет<input id="age" type="number" inputmode="numeric" min="14" max="90" value="32"></label>
        <label class="f">Рост, см<input id="height" type="number" inputmode="numeric" min="120" max="230" value="180"></label>
        <label class="f">Вес, кг<input id="weight" type="number" inputmode="decimal" min="35" max="250" step="0.1" value="84"></label>
        <label class="f">Когда начинаешь<input id="start" type="date" value="2026-11-09"></label>
      </div>
      <div class="cyc" id="cycBox" hidden>
        <label class="check"><input type="checkbox" id="cyc_on" checked>Подстроить тренировки под менструальный цикл</label>
        <div id="cycFields" style="display:grid;gap:12px">
          <div class="grid">
            <label class="f">Первый день последних месячных<input id="cyc_start" type="date" value="2026-11-03"></label>
            <label class="f">Длина цикла, дней<input id="cyc_len" type="number" inputmode="numeric" min="21" max="40" value="28"><small>От начала одних месячных до начала следующих</small></label>
            <label class="f">Сколько дней идут месячные<input id="cyc_per" type="number" inputmode="numeric" min="2" max="8" value="5"></label>
          </div>
          <p class="hint">Нерегулярный цикл, гормональные контрацептивы, беременность или кормление — сними галочку и ориентируйся на самочувствие.</p>
        </div>
      </div>
      <div style="display:grid;gap:8px">
        <b>В какие дни тренируешься? <span class="hint" style="font-weight:400">Отметь 2–5 дней</span></b>
        <div class="daypick" id="dayPick" role="group" aria-label="Дни тренировок"></div>
        <p class="hint" id="dayMsg"></p>
      </div>
      <div style="display:grid;gap:8px">
        <b>Травмы и ограничения</b>
        <div class="seg" id="injSeg" role="group" aria-label="Травмы">
          <button type="button" data-inj="none">Нет</button>
          <button type="button" data-inj="lumbar">Поясница: грыжа, протрузия</button>
          <button type="button" data-inj="knee">Колено</button>
          <button type="button" data-inj="shoulder">Плечо</button>
        </div>
        <div id="injQuick" style="display:grid;gap:12px" hidden></div>
        <p class="hint" id="injMsg">Можно выбрать несколько. Есть другая травма или операция — согласуй программу с врачом и тренером.</p>
      </div>
      <div style="display:grid;gap:8px">
        <b>Твой опыт</b>
        <div class="choices" id="levelChoices" role="group" aria-label="Опыт">
          <button class="choice" data-level="Новичок"><b>Новичок</b><span>Занимаюсь меньше 6 месяцев или после долгого перерыва</span></button>
          <button class="choice" data-level="Средний"><b>Средний</b><span>Регулярно 6 месяцев – 2 года, знаю технику присед, жим, тяги</span></button>
          <button class="choice" data-level="Продвинутый"><b>Продвинутый</b><span>Больше 2 лет, работаю с большими весами</span></button>
        </div>
      </div>
      <details>
        <summary style="cursor:pointer;font-weight:700">Для опытных: мои максимумы (необязательно)</summary>
        <div style="display:grid;gap:12px;margin-top:12px">
          <p class="hint">Максимум — вес, который ты поднимешь 1 раз. Если впишешь — программа подскажет рабочий вес в базовых упражнениях. Не знаешь — посчитай ниже.</p>
          <div class="grid">
            <label class="f">Присед, кг<input id="rm_sq" type="number" inputmode="decimal"></label>
            <label class="f">Жим лёжа, кг<input id="rm_bp" type="number" inputmode="decimal"></label>
            <input id="rm_dl" type="hidden">
            <label class="f">Жим стоя, кг<input id="rm_ohp" type="number" inputmode="decimal"></label>
          </div>
          <div class="row" style="align-items:end">
            <label class="f" style="width:140px">Вес в подходе<input id="c_w" type="number" inputmode="decimal" placeholder="80"></label>
            <label class="f" style="width:140px">Сделал раз<input id="c_r" type="number" inputmode="numeric" placeholder="6"></label>
            <p id="c_out" class="hint" style="padding-bottom:10px">≈ максимум появится здесь</p>
          </div>
        </div>
      </details>
    </div>
  </section>

  <section aria-labelledby="s2">
    <h2 id="s2"><span class="step">2</span>Где и зачем тренируешься</h2>
    <div class="choices" id="placeChoices" role="group" aria-label="Где тренируешься" style="margin-bottom:14px">
      <button class="choice" data-place="gym"><b>В зале</b><span>Штанга, гантели, тренажёры — программа по технике и весам</span></button>
      <button class="choice" data-place="home"><b>Дома, без инвентаря</b><span>Только свой вес, по кругу на время, без прыжков. Для тех, кто только начинает</span></button>
    </div>
    <p class="hint" id="homeGoal" hidden><b>Цель дома:</b> похудеть и укрепить тело. Калории считаем на похудение.</p>
    <div class="choices" id="goalChoices" role="group" aria-label="Цель">
      <button class="choice" data-goal="Жиросжигание"><b>Похудеть</b><span>Убрать жир, сохранить мышцы. Связки упражнений, короткий отдых, дефицит калорий</span></button>
      <button class="choice" data-goal="Набор массы"><b>Набрать мышцы</b><span>Рост мышц. 8–15 повторов, профицит калорий, много белка</span></button>
      <button class="choice" data-goal="Сила"><b>Стать сильнее</b><span>Присед, жим, тяги. Мало повторов, большие веса, калории в норме</span></button>
    </div>
  </section>

  <section aria-labelledby="s3">
    <h2 id="s3"><span class="step">3</span>Сколько есть</h2>
    <div class="panel">
      <p class="hint" id="coefLine"></p>
      <div id="paceBox"><b id="paceLabel">Темп</b><div class="seg" id="paceSeg" style="margin-top:8px"></div></div>
      <div id="flBox" style="display:grid;gap:12px" hidden>
        <div class="grid">
          <label class="f">Хочу сбросить, кг<input id="fl_kg" type="number" inputmode="decimal" min="1" max="80" step="0.5" value="8"></label>
          <label class="f">За сколько месяцев<input id="fl_mo" type="number" inputmode="numeric" min="1" max="24" value="5"></label>
          <label class="f">Новое движение в день
            <select id="fl_mv">
              <option value="0">Не добавлю</option>
              <option value="2500">+2 500 шагов</option>
              <option value="5000" selected>+5 000 шагов</option>
              <option value="7500">+7 500 шагов</option>
            </select>
            <small>Сверх того, что ходишь сейчас: прогулка, пешком до работы, лестница</small>
          </label>
        </div>
        <div class="split" id="splitBox"></div>
      </div>
      <div class="kcal">
        <div class="stat"><span class="lab">Твоя норма</span><span class="val" id="k_tdee">—</span><span class="sub">ккал/день — столько тратишь, вес стоит на месте</span></div>
        <div class="stat main"><span class="lab" id="k_target_lab">Ешь в день</span><span class="val" id="k_target">—</span><span class="sub" id="k_diff">—</span></div>
        <div class="stat"><span class="lab">Прогноз</span><span class="val" id="k_fc">—</span><span class="sub" id="k_fc_sub">—</span></div>
      </div>
      <div style="display:grid;gap:8px">
        <div class="kcal">
          <div class="stat"><span class="lab"><span class="dot" style="background:var(--plate-red)"></span>Белки</span><span class="val" id="m_p">—</span><span class="sub" id="m_p_s">г</span></div>
          <div class="stat"><span class="lab"><span class="dot" style="background:var(--plate-yellow)"></span>Жиры</span><span class="val" id="m_f">—</span><span class="sub" id="m_f_s">г</span></div>
          <div class="stat"><span class="lab"><span class="dot" style="background:var(--plate-blue)"></span>Углеводы</span><span class="val" id="m_c">—</span><span class="sub" id="m_c_s">г</span></div>
        </div>
        <div class="macro-bar" aria-hidden="true"><i id="b_p" style="background:var(--plate-red)"></i><i id="b_f" style="background:var(--plate-yellow)"></i><i id="b_c" style="background:var(--plate-blue)"></i></div>
      </div>
      <p class="note" id="k_note">—</p>
      <p class="hint" id="k_hint"></p>
    </div>
  </section>

  <section aria-labelledby="s4">
    <h2 id="s4"><span class="step">4</span>Программа на 12 недель</h2>
    <div class="panel">
      <div class="cycle">
        <b class="big">12 недель = 3 блока по 4 недели</b>
        <p>Каждый блок решает свою задачу: сначала тело привыкает, потом нагрузка растёт, в конце — пик. Внутри блока недели идут по кругу: <b>вход → накопление → пик → разгрузка</b>. Разгрузка — лёгкая неделя, в неё тело восстанавливается и растёт.</p>
        <p class="hint" id="cycleEnd"></p>
      </div>
      <div style="display:grid;gap:8px"><b>Выбери неделю</b><div class="weekmap" id="weekMap"></div></div>
      <div class="weekinfo" id="weekInfo"></div>
      <div class="note"><b>Перед каждой тренировкой — разминка 10 минут.</b> Она расписана в начале каждой тренировки. Холодные мышцы и суставы — главная причина травм в зале.</div>
      <div class="note" id="levelOrg"></div>
      <div id="injInfo" style="display:grid;gap:12px" hidden></div>
      <div class="cycinfo" id="cycInfo" hidden>
        <b class="big">Программа подстроена под твой цикл</b>
        <p>У каждой тренировки указана фаза. Веса, подходы и подсказки уже пересчитаны под неё.</p>
        <div class="tablewrap" id="cycTable"></div>
      </div>
      <div class="legend">
        <span><span class="dot" style="background:var(--plate-red)"></span>Главное упражнение</span>
        <span><span class="dot" style="background:var(--plate-blue)"></span>Второе по важности</span>
        <span><span class="dot" style="background:var(--plate-yellow)"></span>Дополнительное</span>
        <span><span class="dot" style="background:var(--plate-green)"></span>Пресс / финишер</span>
      </div>
      <p class="hint">В каждом подходе впиши вес и сколько раз сделал. Тоннаж (сколько килограммов ты поднял всего) посчитается сам. Со второй тренировки появится подсказка, добавлять ли вес.</p>
    </div>
    <div id="daysOut" style="display:grid;gap:32px"></div>
    <div class="navrow"><button class="btn ghost" id="prevW">← Прошлая неделя</button><button class="btn" id="nextW">Следующая неделя →</button></div>
    <div class="panel">
      <h3>Тоннаж по неделям, кг</h3>
      <div class="bars" id="bars"></div>
      <p class="hint">Так и должно быть: внутри блока недели 2–3 выше 1-й, неделя разгрузки (серая) ниже на 30–50%.</p>
      <div class="row" id="dlRow"><button class="btn" id="dlHtml">Скачать программу (для печати)</button><button class="btn ghost" id="dlCsv">Скачать таблицу (Excel)</button><span class="hint" id="dlMsg"></span></div>
      <div class="row"><button class="btn ghost" id="copyBtn">Скопировать неделю для тренера</button><span class="hint" id="copyMsg"></span></div>
      <textarea id="copyArea" hidden readonly></textarea>
    </div>
  </section>

  <section aria-labelledby="s5">
    <h2 id="s5"><span class="step">5</span>Что дальше</h2>
    <p class="lead">Три блока — полный цикл. Потом меняем цель, чтобы тело не привыкало.</p>
    <div class="timeline" id="timeline"></div>
    <div class="panel">
      <h3>Как расти каждую неделю</h3>
      <ol class="rules">
        <li>Сначала добавляй повторы. Сделал верхнюю границу во всех подходах (например, 12 из «10–12») — в следующий раз +2,5–5% веса.</li>
        <li>Вес такой, чтобы в последнем подходе оставалось столько повторов в запасе, сколько написано. Слишком легко — тоже ошибка.</li>
        <li>4-я неделя каждого блока — разгрузка. Меньше подходов, легче. Это не лень: в эту неделю тело и растёт.</li>
        <li>Плохой сон, болезнь, пропуск больше недели — повтори прошлую неделю, не беги дальше.</li>
        <li>В конце каждого блока — замеры: вес, талия, фото в одной и той же одежде и свете.</li>
      </ol>
    </div>
    <div class="panel">
      <h3>Словарик</h3>
      <dl class="terms">
        <dt>4 × 10–12</dt><dd>4 подхода, в каждом 10–12 повторений</dd>
        <dt>Тренировки A, B, C</dt><dd>Три разных варианта одной программы. В тренировке A ноги идут первыми, в B — жим, в C — тяга. Тренировки идут по кругу в любом порядке дней</dd>
        <dt>В запасе</dt><dd>Сколько ещё раз ты мог бы сделать, но остановился. «2 в запасе» = тяжело, но не до отказа</dd>
        <dt>Темп 3-0-1</dt><dd>3 секунды опускаешь, без паузы, 1 секунду поднимаешь</dd>
        <dt>Тоннаж</dt><dd>Вес × повторы, сложенные по всем подходам. Показывает, сколько работы ты сделал</dd>
        <dt>Дроп-сет</dt><dd>Сделал подход, сразу снял 20% веса и продолжил ещё раз</dd>
        <dt>Разгрузка</dt><dd>Лёгкая неделя для восстановления</dd>
      </dl>
    </div>
    <div class="row"><button class="btn ghost" id="resetBtn">Очистить мои записи весов</button><span class="hint" id="resetMsg"></span></div>
    <div class="panel rule" id="cycRule" hidden>
      <h3>Главное правило тренировок по циклу</h3>
      <p>Исследования показывают: твои ощущения — боль, усталость, настроение — важнее, чем фаза по календарю. Если по графику фолликулярная фаза, но сил нет из-за недосыпа или стресса — снижай нагрузку без чувства вины. Фазы в программе — ориентир, а не приказ.</p>
    </div>
    <div class="alarm" role="note">
      <h3>Важно</h3>
      <p>Эта программа и расчёт калорий — <b>рекомендация</b>, а не медицинское назначение. Она не учитывает твоё здоровье, травмы и болезни.</p>
      <ul>
        <li>Не пропускай разминку перед тренировкой.</li>
        <li>Если во время тренировки закружилась голова, потемнело в глазах, появилась боль или давление в груди, резкая боль в суставе или спине, тошнота, сильная одышка — <b>сразу прекрати тренировку</b> и обратись к врачу.</li>
        <li>Есть хронические болезни, давление, проблемы с сердцем, травмы, беременность — перед стартом проконсультируйся с врачом.</li>
        <li>Не уверен в технике упражнения — попроси тренера показать. Лучше меньше вес, но правильно.</li>
      </ul>
    </div>
  </section>
</div>

<script>
(function(){
const ROLES=["Основное","Вторичное","Подсобное","Кор/Финишер"];
const PLATE={"Основное":["var(--plate-red)",""],"Вторичное":["var(--plate-blue)",""],"Подсобное":["var(--plate-yellow)","y"],"Кор/Финишер":["var(--plate-green)",""]};
const WEEKNAMES=["Вход","Накопление","Пик месяца","Разгрузка"];
const GOAL_RU={"Жиросжигание":"Похудеть","Набор массы":"Набрать мышцы","Сила":"Стать сильнее"};
const RPE_TXT={"6":"легко, 4 повтора в запасе","7":"3 повтора в запасе","7-8":"2–3 повтора в запасе","8":"2 повтора в запасе","8-9":"1–2 повтора в запасе","10 (тест)":"максимум — тест"};

/* ---------- rules from the methodology (wave periodization: вход→накопление→пик→разгрузка) ---------- */
// w=0: 3×12-15 (вход), w=1: 3×10-12 (накопление), w=2: 3×8-10 (пик), w=3: 2×12-15 (разгрузка)
// Основное получает +dSets из LEVEL_T (+1 для Среднего/Продвинутого)
function fl(m,w,d,role){const rpe=["7","7-8","8","6"][w],tempo=role!=="Кор/Финишер"?"2-0-1":"—";
 if(role==="Кор/Финишер")return{sets:[2,2,3,2][w],reps:["30-45 сек","40-50 сек","45-60 сек","30-45 сек"][w],rpe,rest:"—",tempo};
 const restS={Основное:["90 с","75 с","75 с","90 с"],Вторичное:["75 с","60 с","60 с","75 с"],Подсобное:["60 с","60 с","60 с","60 с"]}[role]||["60 с","60 с","60 с","60 с"];
 const pct=role==="Основное"?[[60,65,70,55],[62,67,72,55],[65,70,75,55]][m-1][w]:null;
 return{sets:[3,3,3,2][w],reps:["12-15","10-12","8-10","12-15"][w],pct,rpe,rest:restS[w],tempo};}
function hy(m,w,d,role){const rpe=["7","7-8","8","6"][w];
 const tempo={Основное:"3-0-1",Вторичное:"3-0-1",Подсобное:"2-0-1","Кор/Финишер":"медленно"}[role];
 if(role==="Кор/Финишер")return{sets:[2,3,3,2][w],reps:["15-20","20-25 сек","20-30 сек","15-20"][w],rpe,rest:"45 с",tempo};
 const pctBase={Основное:[[65,70,75,60],[70,75,80,60],[72,77,82,60]],Вторичное:[[60,65,70,55],[65,70,75,55],[67,72,77,55]]}[role];
 const pct=pctBase?pctBase[m-1][w]:null;
 const restS={Основное:["2 мин","2 мин","2-3 мин","90 с"],Вторичное:["90 с","90 с","2 мин","75 с"],Подсобное:["60 с","60 с","75 с","60 с"]}[role]||["60 с","60 с","60 с","60 с"];
 return{sets:[3,3,3,2][w],reps:["10-12","8-10","6-8","10-12"][w],pct,rpe,rest:restS[w],tempo};}
function st(m,w,d,role){const rpe=["7","8","8-9","6"][w];
 if(role==="Кор/Финишер")return{sets:[3,3,3,2][w],reps:["30-45 сек","40-50 сек","45-60 сек","30-45 сек"][w],rpe,rest:"60 с",tempo:"медленно"};
 const T={Основное:{
  reps:[["5","5","4","5"],["4","3","3","5"],["3","2","2","3"]][m-1],
  pct:[[75,77,80,70],[80,83,85,70],[85,88,90,70]][m-1],
  sets:[3,4,4,3],rest:["3 мин","4 мин","5 мин","2 мин"]},
 Вторичное:{
  reps:[["8-10","6-8","6-8","8-10"],["6-8","5-6","5-6","8-10"],["5-6","4-5","4-5","6-8"]][m-1],
  pct:[[70,72,75,65],[72,75,77,65],[75,78,80,65]][m-1],
  sets:[3,3,3,2],rest:["2 мин","2 мин","2-3 мин","90 с"]},
 Подсобное:{
  reps:[["10-12","8-10","8-10","10-12"],["8-10","6-8","6-8","10-12"],["8-10","6-8","6-8","10-12"]][m-1],
  pct:null,sets:[3,3,3,2],rest:["90 с","90 с","90 с","60 с"]}};
 const tr=T[role]||T.Подсобное;
 return{sets:tr.sets[w],reps:tr.reps[w],pct:tr.pct?tr.pct[w]:null,rpe,rest:tr.rest[w],tempo:role!=="Подсобное"?"вверх взрывно":"2-0-1"};}
const GEN={"Жиросжигание":fl,"Набор массы":hy,"Сила":st};
const LEVEL_T={"Жиросжигание":{"Новичок":[0,0,"Темп 2-0-1: 2 секунды вниз, взрыв вверх. Отдыхай столько, сколько указано. 3 подхода в каждом упражнении."],"Средний":[1,0,"Два главных упражнения — 4 подхода. Остальные — 3. Следи за техникой в последнем подходе."],"Продвинутый":[1,0,"Добавляй суперсеты в упражнениях 3–4 или 5–6: делай их подряд без отдыха, отдыхай после второго."]},
 "Набор массы":{"Новичок":[0,-5,"Обычные подходы. Главное — чувствовать работающую мышцу, а не просто поднимать вес."],"Средний":[1,0,"Два главных — 4 подхода. В последнем подходе останавливайся за 1–2 повтора до отказа."],"Продвинутый":[1,2.5,"Добавляй дроп-сеты в последнем подходе упражнений 3–4: снял 20% веса и продолжил."]},
 "Сила":{"Новичок":[0,-5,"Работаешь на 5–8 повторов. Цель — выучить движение. Каждый повтор одинаково чистый."],"Средний":[1,0,"Два главных — на 1 подход больше. Отдых 3–5 минут. Каждый повтор одинаковый."],"Продвинутый":[1,2.5,"Добавляй скоростные подходы: 50–60% × 3 быстрых подхода × 2–3 повтора после главных."]}};
const WEEK_METHOD={
 "Жиросжигание":{1:["Вход в режим. Учим технику, подбираем веса. 3 подхода, 12–15 повторов.","Добавляем плотность: меньше отдых, точнее техника. 3 подхода, 10–12.","Пик блока: 8–10 повторов, тяжелее вес. Средний/продвинутый — 4 подхода в главных.","Разгрузка: 2 подхода, легче. В конце — замеры."],
  2:["Второй цикл с теми же упражнениями, но уже привычнее. Веса чуть больше прошлого блока.","Добавляем вес +2,5–5 кг к прошлому циклу.","Пик: 8–10 повторов, максимальный рабочий вес за блок.","Разгрузка. Замеры."],
  3:["Третий цикл — веса выше прошлых блоков. 3 подхода.","Работаем с максимальным объёмом.","Пик всего курса. Максимальный вес за 12 недель.","Итоговые замеры за 3 месяца."]},
 "Набор массы":{1:["Вход: 3 подхода, 10–12 повторов, 2 повтора в запасе. Запоминаем рабочий вес.","Накопление: добавляем вес +2,5–5%. 3 подхода.","Пик блока: средний/продвинутый — 4 подхода в главных, 6–8 повторов.","Разгрузка. Замеры: вес, обхваты, фото."],
  2:["Второй цикл с весами выше прошлого блока. 3 подхода.","Прогрессируем: каждую неделю +2,5–5 кг.","Пик: 4 подхода в главных — тяжелее всего за блок.","Разгрузка. Замеры."],
  3:["Третий цикл — самый тяжёлый. 3 подхода.","Пик объёма. Добавляй дроп-сеты (Продвинутый).","Максимальный пик: 4 подхода, рекордные веса.","Итоговые замеры за 3 месяца."]},
 "Сила":{1:["Техника и базовый объём. 3 подхода × 5 повторов. Строго — каждый повтор одинаковый.","+1 подход в главных для Среднего/Продвинутого. Вес +2,5–5%.","Пик: 4 подхода × 4 повтора в главных. Максимально строгая техника.","Разгрузка. 70% от рабочего."],
  2:["Второй цикл тяжелее. 3 подхода × 4 повтора.","Прогрессия. +1 подход в главных для Среднего/Продвинутого.","Пик: 4 подхода × 3 повтора — ощути реальную силу.","Разгрузка."],
  3:["Подводка к максимуму. 3 подхода × 3 повтора — строгая техника.","Пик нагрузки. 4 подхода × 2 повтора в главных.","Тест: 1–3 повтора с максимальным весом. Впиши в шаг 1.","Итоговые замеры и результаты."]}};
const MESO_NAME={"Жиросжигание":["Вход","Объём","Интенсивность"],"Набор массы":["Накопление","Прогрессия","Пик"],"Сила":["Техника","Объём","Пик"]};
const MACRO={
 "Жиросжигание":[["Нед. 1–4","Блок 1 · Вход","3 подхода, 12–15 повторов. Цель — выучить технику и запомнить рабочий вес."],["Нед. 5–8","Блок 2 · Объём","3–4 подхода, 10–12 повторов. Веса растут. Ждём: самочувствие лучше, одежда свободнее."],["Нед. 9–12","Блок 3 · Интенсивность","8–10 повторов, тяжелее. Итоговые замеры."],["Нед. 13+","Дальше · Набор мышц","Поменяй цель на «Набрать мышцы», калории в норму. Через 1–2 месяца — снова «Похудеть», если нужно."]],
 "Набор массы":[["Нед. 1–4","Блок 1 · Накопление","3 подхода, 10–12 повторов. Ждём: рабочие веса +5–10% к 3-й неделе."],["Нед. 5–8","Блок 2 · Прогрессия","3–4 подхода, 8–10 повторов. Веса растут. База для дальнейшего роста."],["Нед. 9–12","Блок 3 · Пик","3–4 подхода, 6–8 повторов. Максимальные веса за курс. Замеры."],["Нед. 13+","Дальше · Сила","Поменяй цель на «Стать сильнее» на 1 месяц, потом снова масса с новыми весами."]],
 "Сила":[["Нед. 1–4","Блок 1 · Техника","5 повторов, 75% от максимума. Цель — навык и чистое движение."],["Нед. 5–8","Блок 2 · Объём","3–4 повтора, 80–85%. Прогрессия каждую неделю."],["Нед. 9–12","Блок 3 · Пик","2–3 повтора, 85–90%. В конце — тест максимума."],["Нед. 13+","Дальше · Набор мышц","Больше мышц = больше силы в следующем цикле. Поменяй цель на «Набрать мышцы»."]]};
const RAMP_D=["Ролл бёдер и спины 2 мин → ягодичный мост 2×10, «мёртвый жук» 2×8 → глубокий присед с поворотом 2×5, выпад с наклоном 2×4 на ногу","Ролл грудного отдела 2 мин → тяга резины к груди 2×15, отжимания лопатками 2×10 → «червяк» + выпад с поворотом 2×4, «ангелы» у стены 2×8","Ролл ягодиц и задней поверхности бедра 2 мин → мост на одной ноге 2×8, «птица-собака» 2×6 → наклон с палкой 2×8, боковой выпад 2×5"];
const RAMP_P={"Жиросжигание":"→ 1 подход первого упражнения с половиной рабочего веса","Набор массы":"→ 2 разминочных подхода первого упражнения: 50%×10, 70%×5","Сила":"→ лесенка в главном: гриф×8, 50%×5, 70%×3, 80%×1"};
const FLR=["Основное","Основное","Вторичное","Вторичное","Подсобное","Подсобное","Кор/Финишер"],HYR=["Основное","Основное","Вторичное","Вторичное","Подсобное","Подсобное","Кор/Финишер"];
const FLB=["1","2","3","4","5","6","7"],HYB=["1","2","3","4","5","6","7"];
// [novice, middle, advanced, link (1RM) for mid, link for adv]
// День A: ноги вперёд → жим → тяга; День B: жим вперёд → тяга → ноги; День C: тяга вперёд → ноги → жим
// Упр. 1-2: тяжёлые со свободным весом; 3-4: тренажёр/чуть легче; 5-6: блочный/изоляция; 7: кор
// Девушки: 3 на низ (таз → колено → таз), 2 тяги, 1 жим по очереди (A — горизонтальный, B — вертикальный, C — горизонтальный)
const TPL_F_DAYS=[
 [["Румынская тяга с гантелями","Румынская тяга со штангой","Румынская тяга со штангой","",""],
  ["Гоблет-присед","Присед со штангой на спине","Присед со штангой на спине","sq","sq"],
  ["Ягодичный мост на полу","Ягодичный мост в тренажёре","Отведение ноги назад в кроссовере","",""],
  ["Тяга горизонтального блока","Тяга горизонтального блока","Тяга гантели одной рукой","",""],
  ["Тяга верхнего блока широким хватом","Тяга верхнего блока","Подтягивания в гравитроне","",""],
  ["Жим в тренажёре от груди","Жим гантелей лёжа","Жим гантелей лёжа","",""],
  ["Планка","Боковая планка","«Мёртвый жук»","",""]],
 [["Ягодичный мост с гантелью","Ягодичный мост со штангой","Ягодичный мост со штангой","",""],
  ["Жим ногами","Болгарские сплит-приседания","Болгарские сплит-приседания с гантелями","",""],
  ["Сгибания ног в тренажёре","Гиперэкстензия на ягодицы","Гиперэкстензия на ягодицы","",""],
  ["Тяга к поясу в рычажном тренажёре","Тяга к поясу в рычажном тренажёре","Тяга штанги в наклоне","",""],
  ["Вертикальная тяга в рычажном тренажёре","Тяга верхнего блока узким хватом","Подтягивания в гравитроне","",""],
  ["Жим в тренажёре сидя","Жим гантелей сидя","Жим гантелей сидя","",""],
  ["Скручивания","Боковая планка","Паллоф-пресс в блоке","",""]],
 [["Румынская тяга с гантелями","Румынская тяга с гантелями","Румынская тяга со штангой","",""],
  ["Выпады с гантелями","Зашагивания на коробку с гантелями","Зашагивания на коробку с гантелями","",""],
  ["Отведение бедра в тренажёре","Отведение ноги назад в кроссовере","Мост на одной ноге","",""],
  ["Тяга гантели одной рукой","Тяга штанги в наклоне","Тяга Т-грифа","",""],
  ["Тяга верхнего блока узким хватом","Тяга верхнего блока широким хватом","Подтягивания","",""],
  ["Жим в тренажёре от груди","Жим гантелей на наклонной","Жим штанги лёжа","","bp"],
  ["Боковая планка","«Мёртвый жук»","Ролик для пресса","",""]]];
const isF=()=>$("sex").value==="f";
const tplSet=()=>isF()?TPL_F_DAYS:TPL[S.goal];
const TPL={"Жиросжигание":[
 // День A — Ноги первые
 [["Гоблет-присед","Присед со штангой на спине","Присед со штангой на спине","sq","sq"],
  ["Румынская тяга с гантелями","Румынская тяга со штангой","Румынская тяга со штангой","",""],
  ["Жим в тренажёре от груди","Жим гантелей лёжа","Жим штанги лёжа","","bp"],
  ["Жим в тренажёре сидя","Жим гантелей сидя","Жим гантелей сидя","",""],
  ["Тяга верхнего блока широким хватом","Тяга верхнего блока","Подтягивания в гравитроне","",""],
  ["Тяга горизонтального блока","Тяга горизонтального блока","Тяга гантели одной рукой","",""],
  ["Планка","Боковая планка","Ролик для пресса","",""]],
 // День B — Жим первый
 [["Жим гантелей лёжа","Жим штанги лёжа","Жим штанги лёжа","bp","bp"],
  ["Жим гантелей сидя","Жим гантелей сидя","Жим штанги стоя","","ohp"],
  ["Тяга верхнего блока широким хватом","Тяга верхнего блока узким хватом","Подтягивания в гравитроне","",""],
  ["Тяга к поясу в рычажном тренажёре","Тяга к поясу в рычажном тренажёре","Тяга штанги в наклоне","",""],
  ["Жим ногами","Болгарские сплит-приседания","Болгарские сплит-приседания с гантелями","",""],
  ["Ягодичный мост на полу","Ягодичный мост со штангой","Гиперэкстензия","",""],
  ["Скручивания","Боковая планка","Подъём ног в висе","",""]],
 // День C — Тяга первая
 [["Тяга верхнего блока","Подтягивания в гравитроне","Подтягивания","",""],
  ["Тяга гантели одной рукой","Тяга штанги в наклоне","Тяга Т-грифа","",""],
  ["Жим ногами","Выпады с гантелями","Шагающие выпады с гантелями","",""],
  ["Сгибания ног в тренажёре","Румынская тяга с гантелями","Гиперэкстензия со штангой","",""],
  ["Жим в тренажёре от груди","Жим гантелей на наклонной","Жим штанги на наклонной","","bp"],
  ["Махи гантелями в стороны","Махи гантелями в стороны","Махи гантелями в стороны","",""],
  ["Боковая планка","«Мёртвый жук»","Паллоф-пресс в блоке","",""]]],
"Набор массы":[
 // День A — Ноги первые
 [["Гоблет-присед","Присед со штангой на спине","Присед со штангой на спине","sq","sq"],
  ["Румынская тяга с гантелями","Румынская тяга со штангой","Румынская тяга со штангой","",""],
  ["Жим в тренажёре от груди","Жим гантелей лёжа","Жим штанги лёжа","","bp"],
  ["Жим в тренажёре сидя","Жим гантелей сидя","Жим гантелей сидя","",""],
  ["Тяга верхнего блока широким хватом","Тяга верхнего блока","Подтягивания в гравитроне","",""],
  ["Тяга горизонтального блока","Тяга горизонтального блока","Тяга гантели одной рукой","",""],
  ["Планка","Боковая планка","Ролик для пресса","",""]],
 // День B — Жим первый
 [["Жим гантелей лёжа","Жим штанги лёжа","Жим штанги лёжа","bp","bp"],
  ["Жим гантелей сидя","Жим гантелей сидя","Жим штанги стоя","","ohp"],
  ["Тяга верхнего блока широким хватом","Тяга верхнего блока узким хватом","Подтягивания в гравитроне","",""],
  ["Тяга к поясу в рычажном тренажёре","Тяга к поясу в рычажном тренажёре","Тяга штанги в наклоне","",""],
  ["Жим ногами","Болгарские сплит-приседания","Болгарские сплит-приседания с гантелями","",""],
  ["Ягодичный мост на полу","Ягодичный мост со штангой","Гиперэкстензия","",""],
  ["Скручивания","Боковая планка","Подъём ног в висе","",""]],
 // День C — Тяга первая
 [["Тяга верхнего блока","Подтягивания в гравитроне","Подтягивания с весом","",""],
  ["Тяга гантели одной рукой","Тяга штанги в наклоне","Тяга Т-грифа","",""],
  ["Жим ногами","Выпады с гантелями","Шагающие выпады с гантелями","",""],
  ["Сгибания ног в тренажёре","Румынская тяга с гантелями","Гиперэкстензия со штангой","",""],
  ["Жим в тренажёре от груди","Жим гантелей на наклонной","Жим штанги на наклонной","","bp"],
  ["Махи гантелями в стороны","Махи гантелями в стороны","Махи гантелями в стороны","",""],
  ["Боковая планка","«Мёртвый жук»","Паллоф-пресс в блоке","",""]]],
"Сила":[
 // День A — Ноги первые
 [["Гоблет-присед","Присед со штангой на спине","Присед со штангой на спине","sq","sq"],
  ["Румынская тяга с гантелями","Румынская тяга со штангой","Румынская тяга со штангой","",""],
  ["Жим в тренажёре от груди","Жим гантелей лёжа","Жим штанги лёжа","","bp"],
  ["Жим в тренажёре сидя","Жим гантелей сидя","Жим штанги стоя","","ohp"],
  ["Тяга верхнего блока широким хватом","Тяга верхнего блока","Подтягивания в гравитроне","",""],
  ["Тяга горизонтального блока","Тяга горизонтального блока","Тяга гантели одной рукой","",""],
  ["Планка","Боковая планка","Ролик для пресса","",""]],
 // День B — Жим первый
 [["Жим гантелей лёжа","Жим штанги лёжа","Жим штанги лёжа с паузой","bp","bp"],
  ["Жим гантелей сидя","Жим гантелей сидя","Жим штанги стоя","","ohp"],
  ["Тяга верхнего блока широким хватом","Тяга верхнего блока узким хватом","Подтягивания в гравитроне","",""],
  ["Тяга к поясу в рычажном тренажёре","Тяга к поясу в рычажном тренажёре","Тяга штанги в наклоне","",""],
  ["Жим ногами","Болгарские сплит-приседания","Болгарские сплит-приседания с гантелями","",""],
  ["Ягодичный мост на полу","Ягодичный мост со штангой","Гиперэкстензия","",""],
  ["«Мёртвый жук»","Боковая планка","Ролик для пресса","",""]],
 // День C — Тяга первая
 [["Тяга верхнего блока","Подтягивания в гравитроне","Подтягивания с весом","",""],
  ["Тяга гантели одной рукой","Тяга штанги в наклоне","Тяга Т-грифа","",""],
  ["Жим ногами","Выпады с гантелями","Болгарские сплит-приседания с гантелями","",""],
  ["Сгибания ног в тренажёре","Румынская тяга с гантелями","Гиперэкстензия со штангой","",""],
  ["Жим в тренажёре от груди","Жим гантелей на наклонной","Жим штанги на наклонной","","bp"],
  ["Махи гантелями в стороны","Махи гантелями в стороны","Махи гантелями в стороны","",""],
  ["Паллоф-пресс в блоке","Паллоф-пресс в блоке","Паллоф-пресс в блоке","",""]]]};
/* ---------- дома без инвентаря: круговая на время, без прыжков ---------- */
// [лёгкий, средний] на каждую позицию; 7 упражнений по кругу
const HOME_TPL=[
 [["Марш на месте с высоким подниманием колен","Быстрый марш на месте с работой рук"],
  ["Присед к стулу","Приседания"],
  ["Отжимания от края дивана","Отжимания с колен"],
  ["Ягодичный мост лёжа","Ягодичный мост на одной ноге"],
  ["Шаг в сторону с руками вверх","Быстрые шаги в сторону (степ-аут)"],
  ["«Птица-собака»","«Скалолаз» в медленном темпе"],
  ["Планка с колен","Планка"]],
 [["Шаги вперёд-назад с хлопком над головой","Выпады назад поочерёдно"],
  ["Выпад назад с опорой о стул","Присед с подъёмом на носки"],
  ["«Лодочка» лёжа на животе","«Лодочка» с разведением рук"],
  ["Подъём на носки стоя","«Конькобежец» шагом, без прыжка"],
  ["Ягодичный мост с паузой","Ягодичный мост с паузой наверху"],
  ["Подъём колена к локтю стоя","Подъём колена к локтю стоя в быстром темпе"],
  ["«Мёртвый жук»","Боковая планка"]],
 [["Марш на месте с хлопками над головой","Высокое поднимание колен в быстром темпе"],
  ["Присед к стулу с паузой","Присед с паузой внизу"],
  ["Отжимания от края дивана","Отжимания с колен"],
  ["Отведение ноги в сторону стоя","Боковые выпады"],
  ["Подъём колена к локтю стоя","«Скалолаз» в медленном темпе"],
  ["«Лодочка» лёжа на животе","«Лодочка» с разведением рук"],
  ["Боковая планка с колен","Планка с касанием плеч"]]];
// по блокам 1–3: работа, отдых (сек), круги
const HOME_LV=[{work:[30,35,40],rest:[30,25,25],rounds:[2,3,3]},{work:[40,40,45],rest:[20,20,15],rounds:[3,3,4]}];
const HOME_ROLE=["Основное","Основное","Вторичное","Вторичное","Подсобное","Подсобное","Кор/Финишер"];
const HOME_MESO=["Знакомство","Темп","Пик"];
const HOME_WEEK=[["Учим движения: спокойный темп, ровное дыхание.","Тот же круг — двигайся увереннее.","Самая бодрая неделя блока.","Лёгкая неделя: на круг меньше, восстанавливаемся."],
 ["Добавили время работы — держи темп до конца.","Темп ровный, отдых не растягивай.","Пик блока: все круги в хорошем темпе.","Лёгкая неделя: на круг меньше."],
 ["Короче отдых, больше кругов — тело уже готово.","Держим темп на всех кругах.","Пик программы — ты уже другой человек, чем 3 месяца назад.","Лёгкая неделя. Дальше — переходи на средний уровень или в зал."]];
const HOME_MACRO=[["Нед. 1–4","Блок 1 · Знакомство","Учим движения, 2–3 круга. Цель — не пропустить ни одной тренировки."],["Нед. 5–8","Блок 2 · Темп","Больше времени работы, отдых короче."],["Нед. 9–12","Блок 3 · Пик","Максимум кругов. Дальше — следующий уровень."]];
const isHome=()=>S.place==="home";
const LEVELS=["Новичок","Средний","Продвинутый"];
const DAYL=["A","B","C"],DAYN=["Пн","Вт","Ср","Чт","Пт","Сб","Вс"];


const RAMP_STEPS=[
 [["Раскатка","ролл бёдер и спины — 2 мин"],["Активация","ягодичный мост 2×10, «мёртвый жук» 2×8"],["Подвижность","глубокий присед с поворотом корпуса 2×5, выпад с наклоном 2×4 на ногу"]],
 [["Раскатка","ролл грудного отдела и широчайших — 2 мин"],["Активация","тяга резины к груди 2×15, отжимания только лопатками 2×10"],["Подвижность","«червяк» + выпад с поворотом 2×4, «ангелы» у стены 2×8"]],
 [["Раскатка","ролл ягодиц и задней поверхности бедра — 2 мин"],["Активация","мост на одной ноге 2×8, «птица-собака» 2×6 на сторону"],["Подвижность","наклон с палкой на спине 2×8, боковой выпад 2×5 на ногу"]]];
const RAMP_PREP={"Жиросжигание":"1 подход первого упражнения с половиной рабочего веса × 10 повторов","Набор массы":"2 разминочных подхода первого упражнения: 50% веса × 10, 70% × 5","Сила":"лесенка в главном упражнении: пустой гриф × 8, 50% × 5, 70% × 3, 80% × 1"};
const KEY="tp-constructor-v2";
let S={goal:"Жиросжигание",level:"Средний",W:0,pace:1,days:[0,2,4],log:{},injs:[]};
const FIELDS=["name","sex","age","height","weight","start","fl_kg","fl_mo","fl_mv","rm_sq","rm_bp","rm_dl","rm_ohp","cyc_start","cyc_len","cyc_per"];
const $=id=>document.getElementById(id);
const num=id=>parseFloat(($(id).value||"").replace(",","."));
const fmt=n=>Math.round(n).toLocaleString("ru-RU");
const dec=n=>String(n).replace(".",",");
const esc=s=>String(s).replace(/[&<>"]/g,c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[c]));
const dash=s=>String(s).replace(/(\d)-(\d)/g,"$1–$2");
const plural=(n,a,b,c)=>{const m10=n%10,m100=n%100;return m10===1&&m100!==11?a:(m10>=2&&m10<=4&&(m100<12||m100>14))?b:c;};
let touched=false;
/* Storage: on FitTrack the server injects window.TP_API = {state, saveUrl, csrf}; otherwise browser storage */
const API=window.TP_API||null;let _saveT=null;
function load(){try{let d=null;if(API&&API.state&&API.state.S)d=API.state;else{const raw=localStorage.getItem(KEY);if(raw)d=JSON.parse(raw);}if(!d)return false;Object.assign(S,d.S||{});S.log=S.log||{};
 FIELDS.forEach(f=>{if(d.f&&d.f[f]!==undefined)$(f).value=d.f[f]});if(d.cycOn!==undefined)$("cyc_on").checked=d.cycOn;touched=!!d.touched;return true;}catch(e){return false}}
function save(){const f={};FIELDS.forEach(k=>f[k]=$(k).value);const body=JSON.stringify({S,f,touched,cycOn:$("cyc_on").checked});
 try{localStorage.setItem(KEY,body)}catch(e){}
 if(API&&API.saveUrl){clearTimeout(_saveT);_saveT=setTimeout(()=>{fetch(API.saveUrl,{method:"PUT",credentials:"same-origin",headers:{"Content-Type":"application/json","Accept":"application/json","X-CSRF-TOKEN":API.csrf||""},body})
  .then(r=>{const el=document.getElementById("syncMsg");if(el)el.textContent=r.ok?"Сохранено в личном кабинете":"Не удалось сохранить — проверь интернет";})
  .catch(()=>{const el=document.getElementById("syncMsg");if(el)el.textContent="Не удалось сохранить — проверь интернет";});},1200);}}

/* ---------- calories ---------- */
const PACE={"Жиросжигание":[["Мягко",-0.10],["Оптимально",-0.15],["Быстро",-0.20]],"Набор массы":[["Чисто",0.05],["Оптимально",0.10],["Быстро",0.15]],"Сила":[["В норме",0],["Чуть больше",0.05]]};
function renderKcal(){
 const g=S.goal,fat=g==="Жиросжигание",opts=PACE[g];if(S.pace>=opts.length)S.pace=1;
 $("paceBox").hidden=fat;$("flBox").hidden=!fat;
 if(!fat){$("paceLabel").textContent=g==="Набор массы"?"Насколько быстро набираем":"Калории для силы";
  $("paceSeg").innerHTML=opts.map((o,i)=>`<button type="button" aria-pressed="${i===S.pace}" data-pace="${i}">${o[0]} · ${o[1]>0?"+":""}${Math.round(o[1]*100)}%</button>`).join("");}
 const nd=N(),coef=isHome()?1.375:nd>=4?1.55:1.375;
 $("coefLine").textContent=`Коэффициент активности ${dec(coef)} — ${nd>=4?"4–5":"1–3"} ${plural(nd,"тренировка","тренировки","тренировок")} в неделю (из шага 1). Тренировки уже внутри нормы — второй раз их не прибавляем.`;
 $("k_hint").textContent=fat?"Считаем по формуле Миффлина — Сан Жеора. Формула ошибается до 10%, поэтому раз в 2–3 недели сверяйся с весами: вес стоит — убери ещё 100–150 ккал; уходит быстрее 1% веса в неделю — добавь немного еды."
  :"Считаем по формуле Миффлина — Сан Жеора. Это точка старта: смотри средний вес за неделю. Вес стоит 2–3 недели — добавь 150–200 ккал; растёт заметно быстрее плана вместе с талией — убери 150–200.";
 const sex=$("sex").value,a=num("age"),h=num("height"),w=num("weight");
 const blank=m=>{["k_tdee","k_target","k_fc","m_p","m_f","m_c"].forEach(i=>$(i).textContent="—");$("k_note").textContent=m;$("splitBox").innerHTML="";};
 if(!(a>0&&h>0&&w>0))return blank("Впиши возраст, рост и вес в шаге 1.");
 const bmr=10*w+6.25*h-5*a+(sex==="m"?5:-161),tdee=bmr*coef;
 let target,p,pTxt,f,fBase,note,floored=false;
 $("k_tdee").textContent=fmt(tdee);
 if(fat){const kg=num("fl_kg"),mo=num("fl_mo");
  if(!(kg>0&&mo>0))return blank("Впиши, сколько хочешь сбросить и за сколько месяцев.");
  if(kg>=w-30)return blank("Проверь цифру «хочу сбросить» — получается слишком маленький вес.");
  const need=kg*7700/(mo*30.4),steps=+$("fl_mv").value,mvK=Math.round(steps*0.0005*w/10)*10;
  const needR=Math.round(need);let foodReal=Math.max(0,needR-mvK);target=Math.round(tdee)-foodReal;if(target<bmr){target=Math.round(bmr);floored=true;foodReal=Math.round(tdee)-target;}
  const pctWeek=need*7/7700/w*100,maxDaily=w*0.01*7700/7,minMo=Math.ceil(kg*7700/(maxDaily*30.4));
  const desired=w-kg;p=Math.round(desired*1.8);pTxt=`г · 1,6–2 г × желаемый вес ${dec(Math.round(desired*10)/10)} кг = ${Math.round(desired*1.6)}–${Math.round(desired*2)} г`;
  fBase=desired;f=Math.round(desired*0.8);
  $("k_target_lab").textContent="Ешь в день";$("k_diff").textContent=`ккал — минус ${fmt(foodReal)} к норме из еды`;
  const end=(()=>{const st=$("start").value;if(!st)return"";const d=new Date(st+"T00:00:00");if(isNaN(d))return"";d.setMonth(d.getMonth()+Math.round(mo));return d.toLocaleDateString("ru-RU");})();
  $("k_fc").textContent=`−${dec(kg)} кг`;$("k_fc_sub").textContent=`≈ −${dec((need*7/7700).toFixed(2))} кг в неделю · ${dec(Math.round(desired*10)/10)} кг${end?` к ${end}`:""}`;
  $("splitBox").innerHTML=`<b>Нужный дефицит ${fmt(needR)} ккал в день — делим на еду и движение</b>
   <div class="eq"><span>Еда <b>−${fmt(foodReal)}</b></span>+<span>Движение <b>−${fmt(mvK)}</b>${steps?` (+${fmt(steps)} шагов)`:""}</span>=<span>Дефицит <b>${fmt(foodReal+mvK)}</b> ккал</span></div>
   <p class="hint">${steps?"Новое движение — то, чего в твоём дне сейчас нет. 1 000 шагов ≈ "+Math.round(1000*0.0005*w)+" ккал при твоём весе.":"Без нового движения весь дефицит ложится на еду — держаться так тяжелее, голод накапливается."}</p>
   ${pctWeek>1?`<p class="warnline">Слишком быстро: это ${dec(pctWeek.toFixed(1))}% веса в неделю, а безопасно — до 1%. Поставь хотя бы ${minMo} ${plural(minMo,"месяц","месяца","месяцев")}.</p>`:""}
   ${floored?`<p class="warnline">Еда упёрлась в базовый обмен (${fmt(bmr)} ккал) — ниже не опускаемся, чтобы не терять мышцы. Добавь движение или месяцы.</p>`:""}`;
  note=`Белок считаем от желаемого веса: ${p} г в день. Так уходит жир, а не мышцы. Это примерно ${Math.round(p/25)} порций по 100–120 г мяса, рыбы или творога.`;}
 else{const adj=opts[S.pace][1];target=Math.round(tdee*(1+adj)/10)*10;p=Math.round(w*1.8);pTxt=`г · 1,8 г на кг веса`;fBase=w;f=Math.round(w*1.0);
  const diff=Math.round(target-tdee);$("k_target_lab").textContent=g==="Набор массы"?"Ешь в день (профицит)":"Ешь в день";
  $("k_diff").textContent=diff===0?"ккал — ровно норма":`ккал — это +${fmt(diff)} к норме`;
  if(g==="Набор массы"){const lo=Math.round(w*0.01*10)/10,hi=Math.round(w*0.015*10)/10,lv={"Новичок":[lo,hi],"Средний":[Math.round(lo*5)/10,Math.round(hi*5)/10],"Продвинутый":[Math.round(lo*2.5)/10,Math.round(hi*2.5)/10]}[S.level];
   $("k_fc").textContent=`+${dec(lv[0])}–${dec(lv[1])} кг`;$("k_fc_sub").textContent="в месяц — нормальный набор для твоего опыта";
   note=`Мышцы растут из профицита + силовых + белка. Не набирай быстрее прогноза — лишнее уйдёт в жир. Белок ${p} г ≈ ${Math.round(p/25)} порций мяса, рыбы, яиц или творога.`;}
  else{$("k_fc").textContent="± 0,5 кг";$("k_fc_sub").textContent="в месяц — вес почти стоит, растут веса на штанге";
   note=`Для силы калории в норме или чуть выше. Углеводы — топливо для тяжёлых подходов, не урезай их.`;}}
 const c=Math.max(0,Math.round((target-p*4-f*9)/4));
 $("k_target").textContent=fmt(target);$("m_p").textContent=p;$("m_f").textContent=f;$("m_c").textContent=c;
 $("m_p_s").textContent=`${pTxt} · ${fmt(p*4)} ккал`;$("m_f_s").textContent=`г · ${dec(fat?0.8:1)} г на кг${fat?" желаемого":""} веса · ${fmt(f*9)} ккал`;$("m_c_s").textContent=`г · остаток · ${fmt(c*4)} ккал`;
 const tot=p*4+f*9+c*4||1;$("b_p").style.width=(p*4/tot*100)+"%";$("b_f").style.width=(f*9/tot*100)+"%";$("b_c").style.width=(c*4/tot*100)+"%";
 $("k_note").textContent=note;}

/* ---------- program over 12 weeks ---------- */
const N=()=>S.days.length;
const tplOf=(W,i)=>(W*N()+i)%3;
function homeRow(W,t,s,i){
 const lv=S.level==="Новичок"?0:1,H=HOME_LV[lv],b=Math.floor(W/4),w=W%4,dl=w===3;
 const e={role:HOME_ROLE[s],block:String(s+1),name:HOME_TPL[t][s][lv],sets:Math.max(1,H.rounds[b]-(dl?1:0)),reps:`${H.work[b]-(dl?5:0)} сек`,rest:`${H.rest[b]+(dl?5:0)} с`,tempo:"—",rec:null,rpe:"",
  rpeTxt:lv?"бодро, но можешь говорить":"спокойно, дыхание ровное"};
 const ph=phaseFor(W,i);e.ph=ph;
 if(ph&&(ph.key==="mens"||ph.key==="pms")){e.sets=Math.max(1,e.sets-1);e.rpeTxt="очень легко, по самочувствию";}
 return e;}
function exRow(W,t,s,i){
 if(isHome())return homeRow(W,t,s,i);
 const g=S.goal,lv=S.level,li=LEVELS.indexOf(lv),fat=g==="Жиросжигание",m=Math.floor(W/4)+1,w=W%4;
 const role=(fat?FLR:HYR)[s],block=(fat?FLB:HYB)[s],tp=tplSet()[t][s];
 const p=GEN[g](m,w,t,role),[dSets,dPct]=LEVEL_T[g][lv];
 let reps=(lv==="Новичок"&&p.nov)?p.nov:p.reps;
 if(lv==="Новичок"){const lu=lowUp(reps);if(lu[1]>0&&lu[0]<5)reps="5-6";}
 const sets=p.sets+(role==="Основное"?dSets:0);
 const pct=p.pct!=null?p.pct+dPct:null;
 const link=li===1?tp[3]:li===2?tp[4]:"";
 const rm=link?num("rm_"+link):NaN;
 const rec=(pct&&rm>0)?Math.round(rm*pct/100/2.5)*2.5:null;
 const e={role,block,name:tp[li],sets,reps,rpe:p.rpe,rest:p.rest,tempo:p.tempo,rec,rpeTxt:RPE_TXT[p.rpe]||""};
 if(S.injs.length){const nm=safeNames(t)[s];if(nm!==e.name){e.orig=e.name;e.name=nm;e.rec=null;}
  if(/тест/.test(e.reps)){e.reps="3-5";e.rpe="7";e.rpeTxt="2–3 повтора в запасе";}
  {const lu=lowUp(e.reps);if(lu[1]>0&&lu[0]<5){e.reps="5-6";e.rec=null;}}
  if(e.tempo==="вверх взрывно")e.tempo="2-0-1, под контролем";
  if(["8-9","9","10 (тест)"].includes(e.rpe))e.rpeTxt="2 повтора в запасе — не ближе к отказу";
 }
 const ph=phaseFor(W,i);e.ph=ph;
 if(ph){
  if(ph.dSets&&w!==3&&e.sets>2)e.sets+=ph.dSets;
  if(ph.rpe&&!/тест/.test(e.rpe)&&e.rpe!=="6")e.rpeTxt=ph.rpe;
  if(e.rec&&ph.mul<1)e.rec=Math.round(e.rec*ph.mul/2.5)*2.5;
  if((ph.key==="mens"||ph.key==="pms")&&role==="Кор/Финишер"){e.name="Спокойная ходьба или растяжка";e.reps="10-15 мин";e.sets=1;e.rest="—";e.tempo="—";e.rpeTxt="комфортно";}
 }
 return e;}
const PH={mens:{name:"Менструальная фаза",mul:0.75,dSets:-1,rpe:"легко, 3–4 повтора в запасе",col:"var(--plate-red)",adv:"Снизь веса на 20–30% и сделай на 1 подход меньше. Без прыжков, скручиваний и сильного натуживания. Болит живот или нет сил — замени тренировку прогулкой и растяжкой."},
 foll:{name:"Фолликулярная фаза",mul:1,dSets:0,col:"var(--plate-green)",adv:"Энергии больше, мышцы быстрее восстанавливаются. Лучшее время прибавлять в весах — работай по плану."},
 ovu:{name:"Овуляторная фаза",mul:1,dSets:0,col:"var(--plate-blue)",adv:"Пик силы и выносливости, но связки мягче — выше риск растяжений. Разминка 15 минут, без резких движений под большим весом."},
 lut:{name:"Лютеиновая фаза",mul:0.95,dSets:0,rpe:"2–3 повтора в запасе",col:"var(--plate-yellow)",adv:"Устаёшь быстрее, может держаться вода. Веса чуть легче (−5%), до отказа не доходи."},
 pms:{name:"Перед месячными (ПМС)",mul:0.65,dSets:-1,rpe:"легко, 3–4 повтора в запасе",col:"var(--muted)",adv:"Разгрузка: снизь веса на 30–40% и сделай на 1 подход меньше — или замени тренировку прогулкой. Тяжесть сейчас — гормоны, а не слабость."}};
const clamp=(v,a,b)=>Math.min(b,Math.max(a,v));
function cycleOn(){return $("sex").value==="f"&&$("cyc_on").checked&&!!$("cyc_start").value;}
function cycLen(){return clamp(Math.round(num("cyc_len"))||28,21,40);}
function cycPer(){return clamp(Math.round(num("cyc_per"))||5,2,8);}
function phaseOfDay(day){const L=cycLen(),P=cycPer(),ov=L-14;return day<=P?"mens":day<ov-1?"foll":day<=ov+1?"ovu":day>L-4?"pms":"lut";}
function phaseFor(W,i){if(i==null||!cycleOn())return null;const d=sessDate(W,i),s0=new Date($("cyc_start").value+"T00:00:00");if(!d||isNaN(s0))return null;
 const L=cycLen(),diff=Math.round((d-s0)/86400000),day=((diff%L)+L)%L+1,k=phaseOfDay(day);return Object.assign({key:k,day},PH[k]);}
function lowUp(reps){if(/сек| м|мин|раунд|тест/.test(reps))return[0,0];const mm=String(reps).match(/^(\d+)(?:-(\d+))?/);if(!mm)return[0,0];const lo=+mm[1];return[lo,mm[2]?+mm[2]:lo];}
const lk=(W,i,s)=>`${S.goal}|${S.level}|${S.days.join("")}${S.injs.length?"|"+S.injs.slice().sort().join(""):""}${isF()?"|f":""}${isHome()?"|home":""}|${W}|${i}|${s}`;
function setVals(W,i,s,e){const L=S.log[lk(W,i,s)]||{},arr=L.sets||[],lo=lowUp(e.reps)[0];let last=0;const out=[];
 for(let k=0;k<e.sets;k++){const c=arr[k]||{};let w=parseFloat(c.w),r=parseFloat(c.r);if(w>0)last=w;else if(r>0)w=last;
  if(w>0&&!(r>0))r=lo;out.push(w>0&&r>0?{w,r}:null);}return out;}
function exTon(W,i,s,e){return setVals(W,i,s,e).reduce((a,v)=>a+(v?v.w*v.r:0),0);}
function sessTon(W,i){const t=tplOf(W,i);let x=0;for(let s=0;s<7;s++)x+=exTon(W,i,s,exRow(W,t,s,i));return x;}
function weekTon(W){let x=0;for(let i=0;i<N();i++)x+=sessTon(W,i);return x;}
function sessDate(W,i){const st=$("start").value;if(!st)return null;const d0=new Date(st+"T00:00:00");if(isNaN(d0))return null;const mon=new Date(d0);mon.setDate(d0.getDate()-((d0.getDay()+6)%7));
 const dt=new Date(mon);dt.setDate(mon.getDate()+W*7+S.days[i]);return dt;}
function dateFor(W,i){const dt=sessDate(W,i);if(!dt)return"";return `${DAYN[S.days[i]]}, ${String(dt.getDate()).padStart(2,"0")}.${String(dt.getMonth()+1).padStart(2,"0")}`;}
function findPrev(W,i,s){const t=tplOf(W,i);let w=W,k=i-1;
 while(w>=0){if(k<0){w--;k=N()-1;continue;}if(w%4!==3&&tplOf(w,k)===t){const e=exRow(w,t,s,k),v=setVals(w,k,s,e).filter(Boolean);if(v.length)return{W:w,e,v};}k--;}return null;}
function tip(W,i,s,e){const I=sugInfo(W,i,s,e);if(!I||!I.why)return"";
 return`<span class="tip ${I.cls}">${{up:"▲",down:"▼",same:"="}[I.cls]} ${I.why} → ставь ${dec(I.w)} кг</span>`;}
function heavier(w){const st=w<20?1:2.5;return Math.max(w+st,Math.ceil(w*1.05/st)*st);}
function rr(x){return x<20?Math.round(x):Math.round(x/2.5)*2.5;}
// Вес, снятый в лёгкую фазу цикла, пересчитывается обратно в рабочий — иначе скидки копятся и прогресс теряется.
function baseW(P){const m=Math.max(...P.v.map(x=>x.w)),mu=P.e.ph?.mul||1;if(!(mu<1))return m;const x=m/mu-0.01,st=x<20?1:2.5;return Math.ceil(x/st)*st;}
function sugInfo(W,i,s,e){const P=findPrev(W,i,s);if(!P)return null;
 const b=baseW(P),mu=e.ph?.mul||1,dl=W%4===3,easy=(P.e.ph?.mul||1)<1,[lo,up]=lowUp(P.e.reps),nup=lowUp(e.reps)[1];
 let t=b,why="",cls="same";
 if(dl){why=`Разгрузка: 80% от ${dec(b)} кг`;t=b*0.8;}
 else if(W%4===0&&W>=4){const B=findPrev(W-3,i,s);cls="up";
  if(B&&B.W===W-4){t=heavier(baseW(B));why=`Новый блок: +5% к 1-й неделе прошлого блока (${dec(baseW(B))} кг)`;}else{t=b*0.9;why=`Новый блок: 90% от ${dec(b)} кг`;}}
 else if(!up){t=b;}
 else if(nup&&nup<up){t=heavier(b);cls="up";why=`Повторов меньше, чем прошлый раз — вес больше (было ${dec(b)} кг)`;}
 else if(easy){why=`Прошлая тренировка была облегчена по циклу — твой рабочий вес ${dec(b)} кг`;}
 else if(P.v.length>=P.e.sets&&P.v.every(x=>x.r>=up)){t=b+(b<20?1:2.5);cls="up";why=`Прошлый раз ${dec(b)} кг на ${up}+ во всех подходах`;}
 else if(P.v.some(x=>x.r<lo)){cls="down";why=`Прошлый раз ${dec(b)} кг было тяжело — оставь вес или сними 5–10%`;}
 else why=`Оставь ${dec(b)} кг и добавь по 1 повтору`;
 if(mu<1){if(dl){if(mu<0.8){t=b*mu;why=`Разгрузка совпала с фазой «${e.ph.name}»: −${Math.round((1-mu)*100)}% от ${dec(b)} кг`;}}
  else{why+=`${why?". ":""}По плану ${dec(rr(t))} кг, по циклу −${Math.round((1-mu)*100)}%`;t=t*mu;cls="same";}}
 return{w:rr(t),why,cls};}
function sugW(W,i,s,e){const I=sugInfo(W,i,s,e);return I?I.w:null;}
function scheme(e){const lu=lowUp(e.reps);
 if(lu[1]===0){if(e.sets>1)return`<div class="scheme t"><div><b>${e.sets}</b><span>${isHome()?plural(e.sets,"круг","круга","кругов"):plural(e.sets,"подход","подхода","подходов")}</span></div><i>×</i><div><b>${esc(dash(e.reps))}</b><span>${isHome()?"работа":"время / дистанция"}</span></div></div>`;
  return`<div class="scheme t"><div><b>${esc(dash(e.reps))}</b><span>работа / отдых</span></div></div>`;}
 const lab=/тест/.test(e.reps)?"тест":plural(lu[1],"повтор","повтора","повторов");
 return`<div class="scheme"><div><b>${e.sets}</b><span>${plural(e.sets,"подход","подхода","подходов")}</span></div><i>×</i><div><b>${esc(dash(e.reps))}</b><span>${lab}</span></div></div>`;}
function renderWeekMap(){const g=S.goal;let h="";
 for(let m=0;m<3;m++){h+=`<div class="blk"><span class="bt"><b>Блок ${m+1}</b> · ${MESO_NAME[g][m]}</span><div class="wk">`;
  for(let w=0;w<4;w++){const W=m*4+w;h+=`<button type="button" class="${w===3?"dl":""}" aria-pressed="${W===S.W}" data-w="${W}" aria-label="Неделя ${W+1}${w===3?", разгрузка":""}">${W+1}</button>`;}
  h+=`</div></div>`;}
 $("weekMap").innerHTML=h;}
function renderProgram(){
 const g=S.goal,W=S.W,m=Math.floor(W/4)+1,w=W%4;
 renderWeekMap();renderCycleInfo();renderInj();
 $("weekInfo").innerHTML=isHome()?`<b>Неделя ${W+1} из 12 · блок ${m} «${HOME_MESO[m-1]}» · дома</b><span>${esc(HOME_WEEK[m-1][w])}</span>`:`<b>Неделя ${W+1} из 12 · блок ${m} «${MESO_NAME[g][m-1]}» · ${WEEKNAMES[w].toLowerCase()}</b><span>${esc(WEEK_METHOD[g][m][w])}</span>`;
 $("levelOrg").innerHTML=isHome()?`<b>Как делать (${S.level==="Новичок"?"лёгкий":"средний"} уровень):</b> ${S.level==="Новичок"?"Без прыжков и без нагрузки на колени. Держись за стул, если нужно. Должно быть тепло и чуть запыхался — но не задыхаешься.":"Без прыжков, но в быстром темпе. Должно быть жарко, говорить можно короткими фразами."}`:`<b>Как делать (${S.level.toLowerCase()}):</b> ${esc(LEVEL_T[g][S.level][2])}`;
 const endD=(()=>{const st=$("start").value;if(!st)return"";const d=new Date(st+"T00:00:00");if(isNaN(d))return"";d.setDate(d.getDate()+84);return d.toLocaleDateString("ru-RU",{day:"numeric",month:"long"});})();
 $("cycleEnd").innerHTML=`Через 12 недель${endD?` (около ${endD})`:""} вернись с новыми данными: вес, замеры, рабочие веса. Обнови шаг 1 — программа и калории пересчитаются под новые показатели.`;
 if(N()<2){$("daysOut").innerHTML=`<div class="panel"><p>Выбери минимум 2 дня тренировок в шаге 1.</p></div>`;renderBars();return;}
 let html="";
 for(let i=0;i<N();i++){const t=tplOf(W,i);let rows="",tot=0;
  for(let s=0;s<7;s++){const e=exRow(W,t,s,i),L=S.log[lk(W,i,s)]||{},ton=exTon(W,i,s,e);tot+=ton;
   const [col,cls]=PLATE[e.role],timed=lowUp(e.reps)[1]===0,isPair=false;
   const meta=[`отдых <b>${esc(e.rest)}</b>`];if(e.rpeTxt)meta.push(e.rpeTxt);if(e.tempo&&e.tempo!=="—")meta.push(`темп ${esc(e.tempo)}`);if(e.rec&&!sugW(W,i,s,e))meta.push(`рабочий вес ≈ <b>${dec(e.rec)} кг</b>`);
   let body="";
   if(timed){body=`<div class="exfoot"><label class="check"><input type="checkbox" id="c_${i}_${s}" data-k="${lk(W,i,s)}" data-f="done" ${L.done?"checked":""}>Сделано</label></div>`;}
   else{const sv=setVals(W,i,s,e);let cells="";const arr=L.sets||[];const sg=sugW(W,i,s,e);let ph=sg?dec(sg):e.rec?dec(e.rec):"";
    for(let k=0;k<e.sets;k++){const c=arr[k]||{};const done=!!sv[k]&&(c.w||c.r);
     cells+=`<div class="set ${done?"done":""}" id="set_${i}_${s}_${k}"><span class="n">${k+1}</span><input aria-label="Подход ${k+1}, вес кг" inputmode="decimal" id="w_${i}_${s}_${k}" data-k="${lk(W,i,s)}" data-set="${k}" data-f="w" value="${esc(c.w??"")}" placeholder="${ph||"кг"}"><em>×</em><input aria-label="Подход ${k+1}, повторы" inputmode="numeric" id="r_${i}_${s}_${k}" data-k="${lk(W,i,s)}" data-set="${k}" data-f="r" value="${esc(c.r??"")}" placeholder="${lowUp(e.reps)[0]}"></div>`;
     if(c.w)ph=c.w;}
    body=`<div class="sets">${cells}</div><div class="exfoot"><span>Тоннаж: <span class="ton" id="t_${i}_${s}">${fmt(ton)} кг</span></span>${tip(W,i,s,e)}</div>`;}
   rows+=`<div class="ex"><span class="plate ${cls}" style="background:${col}">${e.block}</span><div><div class="exname">${esc(e.name)}</div><div class="exmeta">${meta.map(x=>`<span>${x}</span>`).join("")}</div></div>${scheme(e)}
    ${e.orig?`<div class="swapnote"><b>Вместо «${esc(e.orig)}»</b> — бережнее к пояснице</div>`:""}${isPair?`<div class="pair">${e.block} + ${e.block.replace("1","2")}: делай подряд без отдыха, отдых после второго</div>`:""}${body}</div>`;}
  const st=RAMP_STEPS[t],ph=phaseFor(W,i);
  html+=`<article class="panel day"><div class="dayhead"><h3>Тренировка ${i+1} · ${DAYL[t]}</h3><span>${dateFor(W,i)}</span></div>
   ${ph?`<div class="phase" style="--pc:${ph.col}"><span class="pchip">${esc(ph.name)} · ${ph.day}-й день цикла</span><p>${esc(ph.adv)}</p></div>`:""}
   <div class="warm"><h4>Сначала разминка · ${isHome()?"5":ph&&ph.key==="ovu"?"15":"10"} минут. Не пропускай</h4><ol>${warmSteps(t).map(x=>`<li><span><b>${x[0]}:</b> ${esc(x[1])}</span></li>`).join("")}</ol></div>
   ${isHome()?`<p class="hint" style="margin:0 0 4px"><b>По кругу:</b> упражнения подряд — работа и отдых по таймеру, как написано у каждого. Прошёл все 7 — отдых 1 минута и следующий круг.</p>`:""}<div class="exlist">${rows}</div>
   <div class="daytot"><span>${isHome()?"Заминка: 3 минуты спокойно походи по дому + растяжка ног, спины и груди.":"Заминка: 5 минут спокойно на дорожке + растяжка мышц, которые работали."}</span>${isHome()?"":`<span>Тоннаж тренировки: <b id="dt_${i}">${fmt(tot)}</b> кг</span>`}</div></article>`;}
 $("daysOut").innerHTML=html;
 $("prevW").disabled=W===0;$("nextW").disabled=W===11;
 renderBars();}
function renderBars(){const t=[...Array(12)].map((_,W)=>N()>=2?weekTon(W):0),mx=Math.max(...t,1);
 $("bars").innerHTML=t.map((v,W)=>`<div class="bar ${W===S.W?"cur":W%4===3?"dl":""}" title="Неделя ${W+1}: ${fmt(v)} кг"><i style="height:${Math.max(2,v/mx*100)}px"></i><span>${W+1}</span></div>`).join("");}
function renderTimeline(){const g=S.goal,cur=Math.floor(S.W/4);$("timeline").innerHTML=(isHome()?HOME_MACRO:MACRO[g]).map((r,i)=>`<div class="tl ${i===cur?"cur":""}"><span class="w">${r[0]}</span><h3>${esc(r[1].replace("Месяц","Блок"))}${i===cur?" · ты здесь":""}</h3><p>${esc(i===3?r[2]+" Вернись сюда с новыми показателями — пересчитаем.":r[2])}</p></div>`).join("");}
function renderDays(){$("dayPick").innerHTML=DAYN.map((d,i)=>`<button type="button" aria-pressed="${S.days.includes(i)}" data-day="${i}">${d}</button>`).join("");
 const n=N();$("dayMsg").textContent=n<2?"Отметь хотя бы 2 дня.":`${n} ${plural(n,"тренировка","тренировки","тренировок")} в неделю. Тренировки A, B, C идут по очереди${n!==3?" и переходят на следующую неделю":""}.`;}
function renderChoices(){const hm=isHome();document.querySelectorAll("[data-place]").forEach(b=>b.setAttribute("aria-pressed",b.dataset.place===(S.place||"gym")));$("goalChoices").hidden=hm;$("homeGoal").hidden=!hm;
 {const LB=hm?{"Новичок":["Лёгкий","Только начинаю, давно не двигался"],"Средний":["Средний","Немного двигаюсь, хочу бодрее"]}:{"Новичок":["Новичок","Занимаюсь меньше 6 месяцев или после долгого перерыва"],"Средний":["Средний","Регулярно 6 месяцев – 2 года, знаю технику присед, жим, тяги"]};
  document.querySelectorAll("[data-level]").forEach(b=>{const l=LB[b.dataset.level];b.hidden=hm&&b.dataset.level==="Продвинутый";if(l)b.innerHTML=`<b>${l[0]}</b><span>${l[1]}</span>`;});}
 document.querySelectorAll("[data-goal]").forEach(b=>b.setAttribute("aria-pressed",b.dataset.goal===S.goal));document.querySelectorAll("[data-level]").forEach(b=>b.setAttribute("aria-pressed",b.dataset.level===S.level));$("exTag").hidden=touched;}
function renderAll(){renderChoices();renderDays();renderKcal();renderProgram();renderTimeline();}
function markTouched(){if(!touched){touched=true;$("exTag").hidden=true;}}

document.addEventListener("click",ev=>{const b=ev.target.closest("button");if(!b)return;
 if(b.dataset.place){S.place=b.dataset.place;if(S.place==="home"){S.goal="Жиросжигание";if(S.level==="Продвинутый")S.level="Средний";}markTouched();renderAll();save();}
 else if(b.dataset.goal){S.goal=b.dataset.goal;S.pace=1;renderAll();save();}
 else if(b.dataset.level){S.level=b.dataset.level;markTouched();renderAll();save();}
 else if(b.dataset.inj){const k=b.dataset.inj;S.injs=k==="none"?[]:S.injs.includes(k)?S.injs.filter(x=>x!==k):[...S.injs,k];markTouched();renderInj();renderProgram();save();}
 else if(b.dataset.pace){S.pace=+b.dataset.pace;renderKcal();save();}
 else if(b.dataset.day){const d=+b.dataset.day;if(S.days.includes(d)){S.days=S.days.filter(x=>x!==d);}else if(S.days.length<5){S.days=[...S.days,d].sort((a,b)=>a-b);}else{$("dayMsg").textContent="Максимум 5 дней — телу нужны дни отдыха.";return;}
  markTouched();renderDays();renderKcal();renderProgram();save();}
 else if(b.dataset.w){S.W=+b.dataset.w;renderProgram();renderTimeline();save();}
 else if(b.id==="prevW"&&S.W>0){S.W--;renderProgram();renderTimeline();save();$("weekInfo").scrollIntoView({block:"start"});}
 else if(b.id==="nextW"&&S.W<11){S.W++;renderProgram();renderTimeline();save();$("weekInfo").scrollIntoView({block:"start"});}});
function refreshEx(i,s){const W=S.W,e=exRow(W,tplOf(W,i),s,i),sv=setVals(W,i,s,e),L=S.log[lk(W,i,s)]||{},arr=L.sets||[];
 $("t_"+i+"_"+s).textContent=fmt(exTon(W,i,s,e))+" кг";
 for(let k=0;k<e.sets;k++){const c=arr[k]||{};const el=$(`set_${i}_${s}_${k}`);if(el)el.classList.toggle("done",!!sv[k]&&!!(c.w||c.r));}
 $("dt_"+i).textContent=fmt(sessTon(W,i));renderBars();}
document.addEventListener("input",ev=>{const el=ev.target;
 if(el.dataset&&el.dataset.k){const k=el.dataset.k;S.log[k]=S.log[k]||{};
  if(el.dataset.f==="done"){S.log[k].done=el.checked;save();return;}
  const arr=S.log[k].sets=S.log[k].sets||[];const n=+el.dataset.set;arr[n]=arr[n]||{};arr[n][el.dataset.f]=el.value.replace(",",".");
  const p=el.id.split("_");
  if(el.dataset.f==="r"&&el.value){const wi=$(`w_${p[1]}_${p[2]}_${p[3]}`);if(wi&&!wi.value&&parseFloat(wi.placeholder.replace(",","."))>0){wi.value=wi.placeholder;arr[n].w=wi.placeholder.replace(",",".");}}refreshEx(+p[1],+p[2]);save();return;}
 if(FIELDS.includes(el.id)){markTouched();if(el.id.startsWith("rm_")||el.id==="start"||el.id.startsWith("cyc_"))renderProgram();renderKcal();save();}
 if(el.id==="c_w"||el.id==="c_r"){const w=num("c_w"),r=num("c_r");$("c_out").textContent=(w>0&&r>0)?`≈ максимум ${dec(Math.round(w*(1+r/30)*2)/2)} кг`:"≈ максимум появится здесь";}});
document.addEventListener("change",ev=>{if(ev.target.dataset&&ev.target.dataset.f==="done"){const k=ev.target.dataset.k;S.log[k]=S.log[k]||{};S.log[k].done=ev.target.checked;save();return;}
 if(["sex","fl_mv","cyc_on"].includes(ev.target.id)){markTouched();renderKcal();renderProgram();save();}});
$("copyBtn").addEventListener("click",()=>{const W=S.W;let t=`${$("name").value||"Клиент"} — ${GOAL_RU[S.goal]}, ${S.level.toLowerCase()}, неделя ${W+1} из 12 (${WEEKNAMES[W%4].toLowerCase()})\n`;
 for(let i=0;i<N();i++){const tt=tplOf(W,i);t+=`\nТренировка ${i+1} · ${DAYL[tt]} ${dateFor(W,i)}\n`;
  for(let s=0;s<7;s++){const e=exRow(W,tt,s,i),sv=setVals(W,i,s,e);const done=sv.filter(Boolean).map(v=>`${dec(v.w)}×${v.r}`).join(", ");t+=`${e.block} ${e.name}: ${e.sets}×${dash(e.reps)}${done?` — ${done}`:""}\n`;}}
 t+=`\nТоннаж недели: ${fmt(weekTon(W))} кг`;
 const area=$("copyArea");area.value=t;const fb=()=>{area.hidden=false;area.select();$("copyMsg").textContent="Выдели текст ниже и скопируй";};
 try{navigator.clipboard.writeText(t).then(()=>{$("copyMsg").textContent="Скопировано — вставь в чат тренеру";},fb);}catch(e){fb();}});
let resetArmed=false;
$("resetBtn").addEventListener("click",()=>{if(!resetArmed){resetArmed=true;$("resetBtn").textContent="Точно очистить? Нажми ещё раз";$("resetMsg").textContent="";return;}
 S.log={};resetArmed=false;$("resetBtn").textContent="Очистить мои записи весов";$("resetMsg").textContent="Записи очищены";renderProgram();renderBars();save();});

if(!load()){
 const ex={0:[[40,15],[42.5,13],[42.5,12]],2:[[50,12],[50,12],[50,11]],4:[[12,15],[12,15]],5:[[10,15],[10,14]]};
 Object.entries(ex).forEach(([s,arr])=>{S.log[`Жиросжигание|Средний|024|0|0|${s}`]={sets:arr.map(([w,r])=>({w:String(w),r:String(r)}))};});}

function renderCycleInfo(){const fem=$("sex").value==="f";$("cycBox").hidden=!fem;$("cycRule").hidden=!fem;
 const on=cycleOn();$("cycInfo").hidden=!on;$("cycFields").hidden=!$("cyc_on").checked;
 if(!on)return;const Ln=cycLen(),P=cycPer(),ov=Ln-14;
 const rows=[["mens",`1–${P}`,"Энергии меньше, возможны спазмы","Веса −20–30%, на 1 подход меньше, без прыжков и скручиваний. Плохо — прогулка и растяжка"],
  ["foll",`${P+1}–${ov-2}`,"Прилив сил, быстрое восстановление","Работай по плану, прибавляй веса"],
  ["ovu",`${ov-1}–${ov+1}`,"Максимум силы, связки мягче","Тяжёлые базовые по плану, разминка 15 минут, аккуратно с резкими движениями"],
  ["lut",`${ov+2}–${Ln-4}`,"Усталость, задержка воды","Веса −5%, 2–3 повтора в запасе"],
  ["pms",`${Ln-3}–${Ln}`,"ПМС, тяжёлые веса кажутся неподъёмными","Разгрузка: веса −30–40% или прогулка"]];
 $("cycTable").innerHTML=rows.map(r=>`<div class="pcard" style="--pc:${PH[r[0]].col}"><div class="ph1"><b>${PH[r[0]].name}</b><span>дни ${r[1]}</span></div><p><i>Тело:</i> ${r[2]}</p><p><i>Делаем:</i> ${r[3]}</p></div>`).join("");}

/* ---------- downloads ---------- */
/* Downloads on a normal website: a plain file save */
const DL={save:async({filename,data})=>{const blob=data instanceof Blob?data:new Blob([data],{type:filename.endsWith(".xlsx")?"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":"text/html;charset=utf-8"});
 const url=URL.createObjectURL(blob),a=document.createElement("a");a.href=url;a.download=filename;document.body.appendChild(a);a.click();a.remove();setTimeout(()=>URL.revokeObjectURL(url),4000);return{status:"saved"};}};
function csvCell(v){v=String(v??"");return /[;"\n]/.test(v)?'"'+v.replace(/"/g,'""')+'"':v;}
function buildCSV(){const head=["Неделя","Блок","Фаза недели","Тренировка","Дата","Фаза цикла","№","Упражнение","Подходы","Повторы","Отдых","Запас","Рек. вес, кг","Сделано (вес×повт)","Тоннаж, кг"];
 const out=[head];const g=S.goal;
 for(let W=0;W<12;W++)for(let i=0;i<N();i++){const t=tplOf(W,i),ph=phaseFor(W,i);
  for(let s=0;s<7;s++){const e=exRow(W,t,s,i),sv=setVals(W,i,s,e).filter(Boolean);
   out.push([W+1,`${Math.floor(W/4)+1} · ${MESO_NAME[g][Math.floor(W/4)]}`,WEEKNAMES[W%4],`${i+1} · ${DAYL[t]}`,dateFor(W,i),ph?ph.name:"",e.block,e.name,e.sets,dash(e.reps),e.rest,e.rpeTxt,e.rec?dec(e.rec):"",sv.map(v=>`${dec(v.w)}×${v.r}`).join(", "),Math.round(exTon(W,i,s,e))||""]);}}
 return "\uFEFF"+out.map(r=>r.map(csvCell).join(";")).join("\r\n");}
function buildHTML(){const g=S.goal,nm=$("name").value||"Клиент";
 const k=id=>$(id).textContent;let b="";
 for(let W=0;W<12;W++){if(W%4===0)b+=`<h2>Блок ${Math.floor(W/4)+1} · ${MESO_NAME[g][Math.floor(W/4)]}</h2>`;
  b+=`<h3>Неделя ${W+1} · ${WEEKNAMES[W%4]}</h3><p class="m">${esc(WEEK_METHOD[g][Math.floor(W/4)+1][W%4])}</p>`;
  for(let i=0;i<N();i++){const t=tplOf(W,i),ph=phaseFor(W,i);
   b+=`<div class="s"><h4>Тренировка ${i+1} · ${DAYL[t]} — ${dateFor(W,i)}${ph?` · ${esc(ph.name)}`:""}</h4>${ph?`<p class="ph">${esc(ph.adv)}</p>`:""}<p class="w">Разминка ${ph&&ph.key==="ovu"?15:10} мин: ${warmSteps(t).map(x=>esc(x[0]+": "+x[1])).join(" · ")}</p>
   <table><tr><th>№</th><th>Упражнение</th><th>Подходы</th><th>Повторы</th><th>Отдых</th><th>Запас</th><th>Рекомендация</th><th>Вес × повторы (сделано)</th><th>Тоннаж</th></tr>`;
   let tot=0;
   for(let s=0;s<7;s++){const e=exRow(W,t,s,i),sv=setVals(W,i,s,e).filter(Boolean),timed=lowUp(e.reps)[1]===0,sg=timed?null:sugW(W,i,s,e),rc=sg||e.rec,ton=timed?0:exTon(W,i,s,e);tot+=ton;
    b+=`<tr><td>${e.block}</td><td>${esc(e.name)}${e.orig?`<br><small>вместо «${esc(e.orig)}»</small>`:""}</td><td class="c"><b>${e.sets}</b></td><td class="c"><b>${esc(dash(e.reps))}</b></td><td>${esc(e.rest)}</td><td>${esc(e.rpeTxt)}</td><td class="r">${rc?`≈ ${dec(rc)} кг`:""}</td><td>${sv.length?sv.map(v=>`${dec(v.w)}×${v.r}`).join(", "):""}</td><td class="c">${ton?fmt(ton):""}</td></tr>`;}
   b+=`<tr class="tot"><td colspan="8">Тоннаж тренировки</td><td class="c">${tot?fmt(tot)+" кг":"—"}</td></tr></table></div>`;}}
 return `<!doctype html><html lang="ru"><head><meta charset="utf-8"><title>Программа — ${esc(nm)}</title><style>body{font-family:Arial,sans-serif;color:#1A1F29;max-width:900px;margin:24px auto;padding:0 16px;font-size:13px}h1{font-size:24px}h2{margin-top:28px;border-bottom:2px solid #1A1F29}h3{margin:18px 0 4px}.m{color:#555;margin:0 0 8px}.s{break-inside:avoid;margin:10px 0 16px}h4{margin:6px 0}.w,.ph{font-size:12px;color:#444;margin:2px 0 6px}.ph{color:#9a3412}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccd;padding:4px 6px;text-align:left;vertical-align:top}th{background:#eef}td.c{text-align:center;white-space:nowrap}td.r{color:#1E4FD8;font-weight:bold;white-space:nowrap}tr.tot td{background:#f4f5f8;font-weight:bold}tr.tot td:first-child{text-align:right}.box{background:#f4f5f8;padding:10px 14px;border-radius:8px}.warn{border:2px solid #B3261E;padding:10px 14px;border-radius:8px;margin-top:24px}</style></head><body>
 <h1>Программа тренировок — ${esc(nm)}</h1><p>Цель: <b>${GOAL_RU[g]}</b> · уровень: ${S.level.toLowerCase()} · ${N()} ${plural(N(),"тренировка","тренировки","тренировок")} в неделю · 12 недель</p>
 ${INJ_ORDER.filter(k=>S.injs.includes(k)).map(k=>`<div class="warn"><b>${INJ[k].short}.</b> ${INJ[k].warn}</div>`).join("")}<div class="box"><b>Питание:</b> ${k("k_target")} ккал в день (норма ${k("k_tdee")}) · белки ${k("m_p")} г · жиры ${k("m_f")} г · углеводы ${k("m_c")} г</div>${b}
 <div class="warn"><b>Важно.</b> Программа — рекомендация, а не медицинское назначение. Головокружение, боль или давление в груди, резкая боль в суставе, тошнота, сильная одышка — сразу прекрати тренировку и обратись к врачу.${$("sex").value==="f"?" Самочувствие важнее календаря цикла: если нет сил — снижай нагрузку без чувства вины.":""}</div></body></html>`;}
let IN_TG=!!(window.Telegram?.WebApp?.initData||window.TelegramWebviewProxy||/Telegram/i.test(navigator.userAgent));try{IN_TG=IN_TG||sessionStorage.getItem("inTg")==="1"}catch(e){}
// Ссылку берём заранее: Telegram открывает браузер только прямо в ответ на нажатие, ждать сеть нельзя.
let tgLink=null;
function tgPrefetch(){if(!IN_TG||!API?.browserLink)return;fetch(API.browserLink,{method:"POST",credentials:"same-origin",headers:{"Accept":"application/json","X-CSRF-TOKEN":API.csrf||""}}).then(r=>r.ok?r.json():null).then(j=>{if(j?.url)tgLink={url:j.url,at:Date.now()};}).catch(()=>{});}
tgPrefetch();setInterval(()=>{if(!tgLink||Date.now()-tgLink.at>20*60000)tgPrefetch();},5*60000);
async function doSave(kind){
 if(IN_TG&&API?.sendFile){
  // В Telegram файлы не скачиваются — бот присылает их прямо в чат
  $("dlMsg").textContent=kind==="csv"?"Собираю таблицу…":"Собираю программу…";
  try{const data=kind==="csv"?await buildXLSX():buildHTML();
   const fd=new FormData();fd.append("kind",kind==="csv"?"xlsx":"html");fd.append("file",new Blob([data]),kind==="csv"?"p.xlsx":"p.html");
   $("dlMsg").textContent="Отправляю в чат…";
   const r=await fetch(API.sendFile,{method:"POST",credentials:"same-origin",headers:{"Accept":"application/json","X-CSRF-TOKEN":API.csrf||""},body:fd});
   if(r.ok){$("dlMsg").innerHTML="✅ Готово! Файл в чате с ботом — закрой конструктор и посмотри сообщения.";return;}
  }catch(e){}
  $("dlMsg").innerHTML=tgLink?`Не получилось отправить. <a href="${tgLink.url}" style="color:var(--accent)">Открыть в браузере и скачать</a>`:"Не получилось отправить — попробуй ещё раз через минуту.";return;}
 if(IN_TG){const tg=window.Telegram?.WebApp;
  if(tgLink&&tg?.openLink){tg.openLink(tgLink.url);tgLink=null;tgPrefetch();$("dlMsg").innerHTML="Открываю браузер — конструктор откроется уже с твоими данными, там нажми «Скачать» ещё раз.";return;}$("dlMsg").innerHTML='Скачивание не работает внутри Telegram. Открой конструктор в браузере — <a href="https://fittrack.kz/konstruktor/login" style="color:var(--accent)">fittrack.kz/konstruktor/login</a>';return;}
 if(!DL){$("dlMsg").textContent="Скачивание недоступно в этом окне — используй «Скопировать неделю».";return;}
 const TR={а:"a",б:"b",в:"v",г:"g",д:"d",е:"e",ё:"e",ж:"zh",з:"z",и:"i",й:"y",к:"k",л:"l",м:"m",н:"n",о:"o",п:"p",р:"r",с:"s",т:"t",у:"u",ф:"f",х:"h",ц:"ts",ч:"ch",ш:"sh",щ:"sch",ъ:"",ы:"y",ь:"",э:"e",ю:"yu",я:"ya",ә:"a",ғ:"g",қ:"k",ң:"n",ө:"o",ұ:"u",ү:"u",һ:"h",і:"i"};
 const nm=[...($("name").value||"client").toLowerCase()].map(c=>TR[c]??c).join("").replace(/[^a-z0-9_-]+/g,"_").replace(/^_+|_+$/g,"")||"client";
 try{let req;if(kind==="csv"){$("dlMsg").textContent="Собираю таблицу…";const buf=await buildXLSX();req={filename:`Programma_${nm}.xlsx`,data:buf};}else req={filename:`Programma_${nm}.html`,data:buildHTML()};
  await DL.save(req);$("dlMsg").textContent="Файл сохранён";}
 catch(e){const c=e&&e.code;if(e&&e.xl){$("dlMsg").textContent="Не удалось подготовить Excel — проверь интернет и попробуй ещё раз";return;}$("dlMsg").textContent=c==="declined"?"Отменено":c==="rate_limited"?"Подожди пару секунд и нажми снова":"Не получилось скачать в этом окне";if(["unavailable","not_granted","capability_disabled","capability_removed"].includes(c))$("dlRow").hidden=true;}}
$("dlCsv").addEventListener("click",()=>doSave("csv"));$("dlHtml").addEventListener("click",()=>doSave("html"));
if(IN_TG&&API?.sendFile){$("dlHtml").textContent="📩 Прислать программу в Telegram";$("dlCsv").textContent="📩 Прислать таблицу в Telegram";}
if(new URLSearchParams(location.search).has("dl")){$("dlTop").hidden=false;
 $("dlTopHtml").addEventListener("click",()=>doSave("html"));$("dlTopCsv").addEventListener("click",()=>doSave("csv"));
 new MutationObserver(()=>{$("dlTopMsg").innerHTML=$("dlMsg").innerHTML;}).observe($("dlMsg"),{childList:true,characterData:true,subtree:true});
 history.replaceState(null,"",location.pathname);}


/* ---------- lumbar hernia ---------- */
const LUMBAR={
 "Присед со штангой на спине":["Гоблет-присед к скамье","Жим ногами (неполная амплитуда)"],
 "Фронтальный присед со штангой":["Гоблет-присед к скамье","Жим ногами (неполная амплитуда)"],
 "Фронтальный присед":["Гоблет-присед к скамье","Жим ногами (неполная амплитуда)"],
 "Фронтальный присед с гантелями":["Гоблет-присед к скамье","Сплит-присед с опорой рукой"],
 "Присед со штангой (лёгкий, быстрый)":["Сплит-присед с опорой рукой","Гоблет-присед к скамье"],
 "Гоблет-присед":["Гоблет-присед к скамье"],"Гоблет-присед с гантелью":["Гоблет-присед к скамье"],"Гоблет-присед с гирей":["Гоблет-присед к скамье"],
 "Жим ногами":["Жим ногами (неполная амплитуда)"],
 "Румынская тяга со штангой":["Ягодичный мост со штангой","Ягодичный мост с гантелью","Отведение ноги назад в кроссовере"],
 "Румынская тяга с гантелями":["Ягодичный мост с гантелью","Отведение ноги назад в кроссовере","Мост на одной ноге"],
 "Гиперэкстензия":["Отведение ноги назад в кроссовере","«Птица-собака» с паузой 5 сек"],"Гиперэкстензия на ягодицы":["Отведение ноги назад в кроссовере","Ягодичный мост с гантелью"],
 "Разведения гантелей в наклоне":["Разведения гантелей лёжа грудью на наклонной скамье","Тяга каната к лицу"],
 "Махи гирей двумя руками":["Ягодичный мост с гантелью","Отведение ноги назад в кроссовере"],
 "Махи гирей одной рукой":["Ягодичный мост с гантелью","Отведение ноги назад в кроссовере"],
 "Тяга штанги в наклоне":["Тяга гантелей лёжа грудью на наклонной скамье"],
 "Тяга Т-грифа":["Тяга в рычажном тренажёре с упором грудью","Тяга гантелей лёжа грудью на наклонной скамье"],
 "Тяга гантели в наклоне":["Тяга гантели одной рукой с упором в скамью"],
 "Тяга гантели одной рукой":["Тяга гантели одной рукой с упором в скамью"],
 "Жим штанги стоя":["Жим гантелей сидя со спинкой"],"Жим гантелей стоя":["Жим гантелей сидя со спинкой"],
 "Жим гири одной рукой стоя":["Жим гантели одной рукой сидя"],"Швунг жимовой с гирями":["Жим гантелей сидя со спинкой"],
 "Жим гантелей сидя":["Жим гантелей сидя со спинкой"],
 "Жим дьявола":["Жим гантелей лёжа"],
 "Подтягивания с весом":["Подтягивания"],
 "Сгибания рук со штангой":["Сгибания рук с гантелями сидя"],
 "Подъём на носки стоя":["Подъём на носки сидя"],
 "Планка с весом":["Планка"],
 "Скручивания на полу":["Скручивание по Макгиллу"],
 "Подъём ног в висе":["«Мёртвый жук»"],"Ролик для пресса":["«Мёртвый жук»"],
 "Выпады назад со штангой":["Выпады назад с гантелями"],"Шагающие выпады со штангой":["Выпады назад с гантелями","Сплит-присед с опорой рукой"],
 "Трастеры с гантелями":["Эллипс — интервалы"],"Бёрпи + подтягивание":["Велотренажёр со спинкой — интервалы"],
 "Скакалка + бёрпи":["Эллипс — интервалы"],"Махи гирей + скакалка":["Ходьба в горку на дорожке"],
 "Комплекс со штангой (6 упражнений по 5 раз)":["Велотренажёр со спинкой — интервалы"],
 "Гребной тренажёр — интервалы":["Эллипс — интервалы"],"Велотренажёр — интервалы":["Велотренажёр со спинкой — интервалы"],
 "Степ-платформа — интервалы":["Ходьба в горку на дорожке"]};
const KNEE={
 "Присед со штангой на спине":["Присед к скамье (до безболезненной глубины)"],"Фронтальный присед со штангой":["Присед к скамье (до безболезненной глубины)"],
 "Фронтальный присед":["Гоблет-присед к высокой скамье"],"Фронтальный присед с гантелями":["Гоблет-присед к высокой скамье"],
 "Присед со штангой (лёгкий, быстрый)":["Ягодичный мост со штангой","Гоблет-присед к высокой скамье"],
 "Гоблет-присед":["Гоблет-присед к высокой скамье"],"Гоблет-присед с гантелью":["Гоблет-присед к высокой скамье"],"Гоблет-присед с гирей":["Гоблет-присед к высокой скамье"],
 "Гоблет-присед к скамье":["Гоблет-присед к высокой скамье"],"Присед к скамье с гантелью":["Гоблет-присед к высокой скамье"],
 "Жим ногами":["Зашагивания на низкую платформу (15–20 см)","«Стульчик» у стены (изометрия)"],"Жим ногами (неполная амплитуда)":["Зашагивания на низкую платформу (15–20 см)","«Стульчик» у стены (изометрия)"],
 "Болгарские сплит-приседания":["Выпады назад с опорой (неглубоко)"],"Болгарские сплит-приседания с гантелями":["Выпады назад с опорой (неглубоко)"],
 "Сплит-присед":["Выпады назад с опорой (неглубоко)"],"Сплит-присед с опорой рукой":["Выпады назад с опорой (неглубоко)"],
 "Выпады на месте":["Выпады назад с опорой (неглубоко)"],"Выпады назад с гантелями":["Выпады назад с опорой (неглубоко)"],"Выпады назад со штангой":["Выпады назад с опорой (неглубоко)"],
 "Шагающие выпады со штангой":["Выпады назад с опорой (неглубоко)"],"Выпады в сторону с гантелью":["Отведение ноги в кроссовере"],
 "Зашагивания на платформу":["Зашагивания на низкую платформу (15–20 см)"],"Зашагивания на коробку с гантелями":["Зашагивания на низкую платформу (15–20 см)"],"Зашагивания на тумбу с гантелями":["Зашагивания на низкую платформу (15–20 см)"],
 "Разгибания ног в тренажёре":["Разгибания ног в неполной амплитуде (90°→45°)"],"Скандинавские сгибания (только опускание)":["Сгибания ног в тренажёре"],
 "Бёрпи + подтягивание":["Велотренажёр — интервалы"],"Скакалка + бёрпи":["Эллипс — интервалы"],"Махи гирей + скакалка":["Велотренажёр — интервалы"],
 "Степ-платформа — интервалы":["Велотренажёр — интервалы"],"Трастеры с гантелями":["Велотренажёр — интервалы"],
 "Комплекс со штангой (6 упражнений по 5 раз)":["Велотренажёр — интервалы"]};
const SHOULDER={
 "Жим штанги стоя":["Жим в рычаге (лэндмайн) одной рукой с колена"],"Жим гантелей стоя":["Жим в рычаге (лэндмайн) одной рукой с колена"],
 "Жим гантелей сидя":["Жим в рычаге (лэндмайн) одной рукой с колена"],"Жим гантелей сидя со спинкой":["Жим в рычаге (лэндмайн) одной рукой с колена"],
 "Жим гантели одной рукой сидя":["Жим в рычаге (лэндмайн) одной рукой с колена"],"Жим гири одной рукой стоя":["Жим в рычаге (лэндмайн) одной рукой с колена"],
 "Швунг жимовой с гирями":["Жим в рычаге (лэндмайн) одной рукой с колена"],
 "Жим штанги лёжа":["Жим гантелей лёжа нейтральным хватом","Жим штанги с пола (флор-пресс)"],"Жим штанги лёжа с паузой":["Жим штанги с пола (флор-пресс)","Жим гантелей лёжа нейтральным хватом"],
 "Жим лёжа узким хватом":["Жим гантелей лёжа нейтральным хватом","Разгибания рук на блоке"],"Жим гантелей лёжа":["Жим гантелей лёжа нейтральным хватом"],
 "Жим штанги на наклонной":["Жим гантелей на наклонной нейтральным хватом"],"Жим гантелей на наклонной":["Жим гантелей на наклонной нейтральным хватом"],
 "Отжимания на брусьях":["Разгибания рук на блоке","Французский жим с гантелями лёжа"],"Отжимания на брусьях в гравитроне":["Разгибания рук на блоке","Французский жим с гантелями лёжа"],
 "Отжимания от пола":["Отжимания от скамьи (без боли)"],"Жим дьявола":["Жим гантелей лёжа нейтральным хватом","Отжимания от скамьи (без боли)"],
 "Подтягивания":["Тяга верхнего блока нейтральным хватом","Тяга верхнего блока одной рукой"],"Подтягивания с весом":["Тяга верхнего блока нейтральным хватом","Тяга верхнего блока одной рукой"],
 "Подтягивания в гравитроне":["Тяга верхнего блока нейтральным хватом","Тяга верхнего блока одной рукой"],"Подтягивания обратным хватом":["Тяга верхнего блока нейтральным хватом","Тяга верхнего блока одной рукой"],
 "Тяга верхнего блока широким хватом":["Тяга верхнего блока нейтральным хватом","Тяга верхнего блока одной рукой"],
 "Австралийские подтягивания":["Тяга нижнего блока сидя","Тяга гантели одной рукой с упором в скамью"],
 "Махи гантелями в стороны":["Махи в стороны до параллели (чуть вперёд от корпуса)"],"Махи в стороны в блоке":["Махи в стороны до параллели (чуть вперёд от корпуса)"],
 "Подъём ног в висе":["«Мёртвый жук»"],"Ролик для пресса":["«Мёртвый жук»"],
 "Бёрпи + подтягивание":["Велотренажёр — интервалы"],"Скакалка + бёрпи":["Эллипс — интервалы"],"Трастеры с гантелями":["Велотренажёр — интервалы"],
 "Комплекс со штангой (6 упражнений по 5 раз)":["Велотренажёр — интервалы"]};
const INJ_ORDER=["lumbar","knee","shoulder"];
const INJ={
 lumbar:{short:"Грыжа/протрузия поясницы",title:"Программа адаптирована под грыжу поясницы",
  alert:"Тренироваться можно только вне обострения и после разрешения врача (невролога, реабилитолога). Острая боль, боль отдаёт в ногу — сначала к врачу, не в зал.",
  how:["Спина всегда ровная (нейтральная), пресс напряжён в каждом повторе — как будто ждёшь удар в живот.","Никакой штанги на плечах, наклонов вперёд с весом, скручиваний с нагрузкой и прыжков.","Упражнения сидя — со спинкой, тяги — с упором грудью в скамью.","Всегда 2–3 повтора в запасе. Без отказа и без тестов максимума.","Не задерживай дыхание на усилии: выдох на подъёме.","Не тренируйся в первый час после сна — диски утром набухшие и уязвимые.","Ходи пешком 20–40 минут каждый день. Сидишь долго — вставай каждые 30–40 минут."],
  pain:["Во время упражнения — не больше 3 из 10, и боль не уходит в ягодицу или ногу.","На следующий день спина не хуже, чем была до тренировки.","Боль отдаёт в ногу, появилось покалывание — это упражнение убираем совсем."],
  red:["Онемение в паху или промежности, проблемы с мочеиспусканием или стулом — вызывай скорую.","Нарастающая слабость в ноге, стопа «шлёпает», онемение стопы.","Боль сильнее с каждым днём или будит ночью."],
  do:["Гоблет-присед к скамье с ровной спиной","Ягодичный мост и отведения ноги в кроссовере","Тяги с упором грудью в скамью или тренажёр","Жимы сидя со спинкой","«Большая тройка Макгилла» в разминке","Ходьба 20–40 минут каждый день"],
  dont:["Штанга на плечах (присед со штангой, выпады со штангой)","Становая и румынская тяга, махи гирей","Тяги в наклоне без опоры","Скручивания, подъём ног в висе, ролик для пресса","Прыжки, бёрпи, гребной тренажёр","Тесты максимума и подходы до отказа"],
  map:LUMBAR,warn:"Только вне обострения и с разрешения врача. Спина ровная, пресс напряжён, без штанги на плечах, наклонов с весом, скручиваний и прыжков. 2–3 повтора в запасе, без тестов максимума. Боль во время — не больше 3 из 10 и не отдаёт в ногу; отдаёт — упражнение убрать. Онемение в паху, проблемы с мочеиспусканием, слабость в ноге — срочно к врачу."},
 knee:{short:"Колено",title:"Программа адаптирована под больное колено",
  alert:"После свежей травмы, операции или если колено опухло — сначала к врачу (ортопеду, реабилитологу). Эта схема — для хронической боли и восстановления после разрешения врача.",
  how:["Приседай только на безболезненную глубину — до скамьи. Глубину добавляем постепенно, когда боль ушла.","Колено идёт по линии носка, не заваливается внутрь.","Никаких прыжков, бёрпи, скакалки и бега интервалами — кардио на велосипеде или эллипсе.","Выпады — только назад и неглубоко, с опорой рукой. Шагающие и болгарские — пока нет.","Больше работы на ягодицы и заднюю поверхность бедра: мосты, сгибания ног, отведения. Это разгружает колено.","Разгибания ног — только в неполной амплитуде (от 90° до 45°).","Изометрия — «стульчик» у стены 3–5 раз по 30–45 сек — часто снимает боль перед тренировкой."],
  pain:["Во время упражнения — не больше 3 из 10.","Боль проходит в течение суток, а утром колено не хуже, чем было.","Боль острая, «простреливает» — уменьши глубину или вес, не помогло — убери упражнение."],
  red:["Колено опухло, стало горячим.","Колено «заклинило» — не разгибается до конца.","Колено «подкашивается», нет устойчивости.","Не можешь наступить на ногу."],
  do:["Присед к скамье — только на глубину без боли","Выпады назад, неглубоко, с опорой рукой","Мосты, сгибания ног, отведения — разгружают колено","Разгибания ног в неполной амплитуде (90°→45°)","«Стульчик» у стены 3–5 × 30–45 сек","Кардио на велосипеде или эллипсе"],
  dont:["Жим ногами","Глубокие приседания со штангой","Шагающие и болгарские выпады","Прыжки, бёрпи, скакалка, степ","Высокие зашагивания","Разгибания ног в полной амплитуде"],
  map:KNEE,warn:"После свежей травмы или операции — только с разрешения врача. Присед до безболезненной глубины, без прыжков, выпады только назад и неглубоко. Боль во время — не больше 3 из 10 и проходит за сутки. Отёк, «заклинивание», колено подкашивается — к врачу."},
 shoulder:{short:"Плечо",title:"Программа адаптирована под больное плечо",
  alert:"После вывиха, падения на руку, если рука не поднимается или ослабла — сначала к врачу (ортопеду, реабилитологу), не в зал.",
  how:["Без жимов над головой со штангой и гантелями — вместо них жим в рычаге (лэндмайн) под углом.","Жимы лёжа — гантелями нейтральным хватом (ладони друг к другу), локти под углом 45° к корпусу, а не в стороны.","Без отжиманий на брусьях, подтягиваний и висов — вместо них тяга блока нейтральным хватом.","Махи в стороны — только до параллели с полом, руки чуть впереди корпуса.","Тяг должно быть не меньше, чем жимов: лопатки и задняя дельта — главная защита плеча.","Перед каждой тренировкой — разминка с резинкой на вращатели плеча (она в разминке).","Не работай через «щелчок» с болью в верхней точке."],
  pain:["Во время упражнения — не больше 3 из 10.","Ночью плечо не болит сильнее после тренировки, на следующий день не хуже.","Боль в определённой точке амплитуды — сократи амплитуду или убери упражнение."],
  red:["Рука не поднимается или резко ослабла.","Онемение, покалывание, боль уходит в руку до пальцев.","Сильная ночная боль, не даёт спать.","Было падение на руку или вывих."],
  do:["Жим в рычаге (лэндмайн) с колена вместо жимов над головой","Жимы гантелей нейтральным хватом, локти под 45°","Тяга верхнего блока нейтральным хватом","Тяга каната к лицу и вращения с резинкой","Тяг не меньше, чем жимов","Махи в стороны только до параллели"],
  dont:["Жимы штанги и гантелей над головой, швунг","Отжимания на брусьях","Подтягивания и висы","Жим штанги лёжа широким хватом","Махи выше плеч","Бёрпи, трастеры, жим дьявола"],
  map:SHOULDER,warn:"После вывиха или падения — только с разрешения врача. Без жимов над головой, брусьев и подтягиваний, жимы гантелями нейтральным хватом, махи до параллели. Боль во время — не больше 3 из 10. Рука не поднимается, онемение в руке, сильная ночная боль — к врачу."}};
const _safe={};
function safeNames(t){const li=LEVELS.indexOf(S.level),act=INJ_ORDER.filter(k=>S.injs.includes(k)),key=S.goal+"|"+li+"|"+t+"|"+act.join(",")+(isF()?"|f":"");if(_safe[key])return _safe[key];
 const maps=act.map(k=>INJ[k].map),bad=n=>maps.some(m=>m[n]);
 const raw=tplSet()[t].map(r=>r[li]),used=new Set(raw.filter(n=>!bad(n))),out=[];
 raw.forEach(n=>{let cur=n;for(let pass=0;pass<3&&bad(cur);pass++){for(const m of maps){const alts=m[cur];if(!alts)continue;cur=alts.find(a=>!used.has(a)&&!bad(a))||alts.find(a=>!used.has(a))||alts[0];}}
  used.add(cur);out.push(cur);});
 return _safe[key]=out;}
function injPairs(k){const li=LEVELS.indexOf(S.level),pairs=new Map(),m=INJ[k].map;
 for(let t=0;t<3;t++){const sn=safeNames(t);tplSet()[t].forEach((r,s)=>{if(m[r[li]]&&sn[s]!==r[li])pairs.set(r[li],sn[s]);});}return pairs;}
function renderInjQuick(act){$("injQuick").hidden=!act.length;
 $("injQuick").innerHTML=act.map(k=>{const I=INJ[k],pr=injPairs(k);
  return`<div class="iq"><b class="iqt">${esc(I.short)}</b>
  <div class="iqg"><div class="iqc ok"><h4>Что делаем</h4><ul>${I.do.map(x=>`<li>${esc(x)}</li>`).join("")}</ul></div>
  <div class="iqc no"><h4>Чего не делаем</h4><ul>${I.dont.map(x=>`<li>${esc(x)}</li>`).join("")}</ul></div>
  <div class="iqc sw"><h4>Что заменили в твоей программе</h4>${pr.size?`<ul>${[...pr].map(([a,b])=>`<li><s>${esc(a)}</s><br>→ <b>${esc(b)}</b></li>`).join("")}</ul>`:`<p class="hint">Для твоей цели и уровня таких упражнений нет — программа подходит как есть.</p>`}</div></div>
  <p class="hint">${esc(I.alert)} Правило боли и стоп-сигналы — в шаге 4.</p></div>`;}).join("");}
function renderInj(){document.querySelectorAll("[data-inj]").forEach(b=>b.setAttribute("aria-pressed",b.dataset.inj==="none"?S.injs.length===0:S.injs.includes(b.dataset.inj)));
 const act=INJ_ORDER.filter(k=>S.injs.includes(k));renderInjQuick(act);$("injInfo").hidden=!act.length;if(!act.length){$("injInfo").innerHTML="";return;}
 const li=LEVELS.indexOf(S.level),pairs=new Map();
 for(let t=0;t<3;t++){const sn=safeNames(t);tplSet()[t].forEach((r,s)=>{if(sn[s]!==r[li])pairs.set(r[li],sn[s]);});}
 const ul=(a,c)=>`<ul${c?` class="${c}"`:""}>${a.map(x=>`<li>${esc(x)}</li>`).join("")}</ul>`;
 $("injInfo").innerHTML=act.map(k=>{const I=INJ[k];return`<div class="inj"><b class="big">${esc(I.title)}</b><p class="alert">${esc(I.alert)}</p>
  <div class="injgrid"><div><h4>Как тренируемся</h4>${ul(I.how)}</div><div><h4>Правило боли</h4>${ul(I.pain)}</div><div><h4>Срочно к врачу</h4>${ul(I.red,"red")}</div></div></div>`;}).join("")+
  `<details class="inj"><summary style="cursor:pointer;font-weight:700">Что заменили в программе (${pairs.size})</summary><ul class="swaps">${pairs.size?[...pairs].map(([a,b])=>`<li>${esc(a)} → <b>${esc(b)}</b></li>`).join(""):"<li>Для этой цели и уровня замены не понадобились — следи за техникой и правилом боли.</li>"}</ul></details>`;}

function warmSteps(t){if(isHome())return[["Суставы","вращения шеей, плечами, тазом, коленями и стопами — 2 мин"],["Разогрев","марш на месте с руками вверх-вниз — 2 мин"],["Подготовка","каждое упражнение круга по 5 раз медленно"]];
 const g=S.goal,st=RAMP_STEPS[t].map(x=>x.slice());
 if(S.injs.includes("knee")){st[2][1]=st[2][1].replace("глубокий присед с поворотом корпуса 2×5","присед к высокой скамье с поворотом корпуса 2×5").replace(", боковой выпад 2×5 на ногу",", отведение ноги стоя 2×10");
  st.push(["Колени","велосипед 5 мин, «стульчик» у стены на безболезненной глубине 3×30 сек, шаги в сторону с резинкой 2×10"]);}
 if(S.injs.includes("shoulder")){st[2][1]=st[2][1].replace("«червяк» + выпад с поворотом 2×4","выпад с поворотом корпуса 2×4");
  st.push(["Плечи","вращение наружу с резинкой 2×15, разведение резинки перед собой 2×15, скольжение руками по стене 2×8"]);}
 if(S.injs.includes("lumbar")){st[0][1]=st[0][1].replace("ролл бёдер и спины","ролл бёдер и верха спины (поясницу не раскатываем)");
  st[2]=["Подвижность","кошка-корова мягко, без боли 2×6, ходьба 3 мин"];
  st.splice(2,0,["Большая тройка Макгилла","скручивание по Макгиллу 3×10 сек, боковая планка 3×10 сек на сторону, «птица-собака» 3×10 сек на сторону"]);}
 st.push(["Подготовка",RAMP_PREP[g]]);return st;}

/* ---------- styled Excel ---------- */
let _xl=null;
function loadExcel(){if(window.ExcelJS)return Promise.resolve(window.ExcelJS);if(_xl)return _xl;
 _xl=new Promise((res,rej)=>{const s=document.createElement("script");s.src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js";s.onload=()=>res(window.ExcelJS);s.onerror=()=>{_xl=null;rej({xl:1});};document.head.appendChild(s);});return _xl;}
async function buildXLSX(){const X=await loadExcel();const wb=new X.Workbook();const g=S.goal,nm=$("name").value||"Клиент";
 const F="Arial",DARK="FF1A1F29",ACC="FF1E4FD8",SOFT="FFE3EAFC",WARM="FFFFF4D6",INP="FFFFFBEA",GREY="FFE9ECF1";
 const fill=c=>({type:"pattern",pattern:"solid",fgColor:{argb:c}});
 const thin={style:"thin",color:{argb:"FFD3D8E1"}},box={top:thin,left:thin,bottom:thin,right:thin};
 const roleCol={"Основное":"FFD33B2C","Вторичное":"FF1E4FD8","Подсобное":"FFE7B416","Кор/Финишер":"FF2E9A55"};
 /* overview */
 const o=wb.addWorksheet("Обзор",{views:[{showGridLines:false}]});o.columns=[{width:3},{width:30},{width:70}];
 const ot=(r,t,opt={})=>{const c=o.getCell(r,2);c.value=t;c.font={name:F,size:opt.s||11,bold:!!opt.b,color:{argb:opt.c||DARK}};};
 ot(2,`Программа тренировок — ${nm}`,{s:18,b:true});
 const info=[["Цель",GOAL_RU[g]],["Уровень",S.level],["Тренировок в неделю",String(N())+" ("+S.days.map(d=>DAYN[d]).join(", ")+")"],["Длительность","12 недель = 3 блока по 4 недели (вход → накопление → пик → разгрузка)"],
  ["Калории в день",`${$("k_target").textContent} ккал (твоя норма ${$("k_tdee").textContent})`],["Белки / жиры / углеводы",`${$("m_p").textContent} / ${$("m_f").textContent} / ${$("m_c").textContent} г`]];
 if(S.injs.length)info.push(["Ограничения",INJ_ORDER.filter(k=>S.injs.includes(k)).map(k=>INJ[k].short).join(", ")+" — упражнения заменены, см. отметки «вместо …»"]);
 if(cycleOn())info.push(["Цикл","Учитывается: у каждой тренировки указана фаза, веса уже скорректированы"]);
 info.forEach((r,k)=>{const a=o.getCell(4+k,2),b=o.getCell(4+k,3);a.value=r[0];b.value=r[1];a.font={name:F,bold:true};b.font={name:F};a.fill=fill(GREY);[a,b].forEach(c=>{c.border=box;c.alignment={vertical:"middle",wrapText:true};});});
 const pr=4+info.length,PADDR=`'Обзор'!$C$${pr}`;{const a=o.getCell(pr,2),b=o.getCell(pr,3);a.value="Прирост тоннажа (когда повторы те же)";b.value=0.1;b.numFmt="0%";a.font={name:F,bold:true};b.font={name:F,bold:true,color:{argb:"FF0000FF"}};a.fill=fill(GREY);b.fill=fill(INP);[a,b].forEach(c=>{c.border=box;c.alignment={vertical:"middle",horizontal:"left"};});}
 let r=6+info.length;ot(r,"Как заполнять",{b:true,s:13});r++;
 ["Листы «Блок 1–3» — твои тренировки по неделям.","Жёлтые ячейки — вписываешь вес и сколько раз сделал (в среднем за подход). Тоннаж считается сам.","Голубые ячейки — рекомендация: сколько поднять и сколько раз. Считаются сами от того, что ты вписал в прошлый раз.","Хочешь другое упражнение — поменяй название в 1-й неделе (есть список по типу движения, можно вписать своё). На всех следующих неделях оно поменяется само.","«Рек. вес» со 2-й недели считается от того, что ты вписал. Повторов столько же, сколько в прошлый раз, — цель: тоннаж + прирост из ячейки выше (сначала добавляешь повторы, дошёл до верха — вес вверх). Повторов по плану меньше — вес +5%, тоннаж может быть чуть ниже: так задумано.","4-я неделя каждого блока — разгрузка: 80% веса и меньше подходов.","Перед каждой тренировкой — разминка 10 минут (расписана в начале тренировки)."].forEach(t=>{o.getCell(r,3).value="• "+t;o.getCell(r,3).font={name:F};o.getCell(r,3).alignment={wrapText:true};r++;});
 r++;ot(r,"Важно",{b:true,s:13,c:"FFB3261E"});r++;
 const warn=["Программа — рекомендация, а не медицинское назначение.","Головокружение, боль или давление в груди, резкая боль в суставе или спине, тошнота, сильная одышка — сразу прекрати тренировку и обратись к врачу."];
 INJ_ORDER.filter(k=>S.injs.includes(k)&&k!=="lumbar").forEach(k=>warn.push(INJ[k].warn));if(S.injs.includes("lumbar"))warn.push("Грыжа: только вне обострения и с разрешения врача. Боль во время — не больше 3 из 10 и не отдаёт в ногу. Онемение в паху, проблемы с мочеиспусканием, слабость в ноге — срочно к врачу.");
 if($("sex").value==="f")warn.push("Самочувствие важнее календаря цикла: нет сил — снижай нагрузку без чувства вины.");
 warn.forEach(t=>{const c=o.getCell(r,3);c.value="• "+t;c.font={name:F,color:{argb:"FFB3261E"}};c.alignment={wrapText:true};r++;});
 /* blocks */
 const head=["№","Упражнение","Подходы","Повторы","Отдых","Сколько в запасе","Рек. вес, кг","Рек. повт.","Вес, кг","Сделал раз","Тоннаж, кг","Заметки"];
 /* список упражнений по типу движения */
 const li=LEVELS.indexOf(S.level),lists=PAT_ORDER.map(p=>[...PAT_LIST[p]]);
 for(let t=0;t<3;t++)for(let s=0;s<7;s++){const p=PAT_ORDER.indexOf(patSet()[t][s]),nm=S.injs.length?safeNames(t)[s]:tplSet()[t][s][li];if(!lists[p].includes(nm))lists[p].push(nm);}
 const colL=k=>String.fromCharCode(65+k),dv=(t,s)=>{const pt=patSet()[t][s],k=PAT_ORDER.indexOf(pt);return{type:"list",allowBlank:true,showErrorMessage:false,showInputMessage:true,promptTitle:PAT_NAME[pt],prompt:"Выбери из списка или впиши своё. Меняй в 1-й неделе — дальше поменяется само.",formulae:[`'Упражнения'!$${colL(k)}$3:$${colL(k)}$${lists[k].length+2}`]};};
 const src={},hist={},NC=12;
 // Колонки: G рек.вес, H рек.повт, I вес, J сделал раз, K тоннаж, L заметки; скрытые M нижняя граница повторов, N рабочий вес прошлого раза, O его тоннаж.
 for(let m=0;m<3;m++){
  const ws=wb.addWorksheet(`Блок ${m+1}`,{views:[{state:"frozen",ySplit:2,showGridLines:false}],pageSetup:{orientation:"landscape",fitToPage:true,fitToWidth:1,fitToHeight:0}});
  ws.columns=[{width:6},{width:40},{width:10},{width:11},{width:12},{width:24},{width:11},{width:10},{width:11},{width:11},{width:12},{width:34},{width:4,hidden:true},{width:4,hidden:true},{width:4,hidden:true}];
  ws.mergeCells(1,1,1,NC);const t=ws.getCell(1,1);t.value=`БЛОК ${m+1} · ${MESO_NAME[g][m].toUpperCase()} · ${GOAL_RU[g]} · ${nm}`;t.font={name:F,size:14,bold:true,color:{argb:"FFFFFFFF"}};t.fill=fill(DARK);t.alignment={vertical:"middle",indent:1};ws.getRow(1).height=28;
  head.forEach((h,k)=>{const c=ws.getCell(2,k+1);c.value=h;c.font={name:F,bold:true,size:10,color:{argb:"FFFFFFFF"}};c.fill=fill(ACC);c.alignment={horizontal:"center",vertical:"middle",wrapText:true};c.border=box;});ws.getRow(2).height=30;
  let row=3;const weekTotals=[];
  for(let w=0;w<4;w++){const W=m*4+w;
   ws.mergeCells(row,1,row,NC);let c=ws.getCell(row,1);c.value=`НЕДЕЛЯ ${W+1} · ${WEEKNAMES[w].toUpperCase()} — ${WEEK_METHOD[g][m+1][w]}`;c.font={name:F,bold:true,size:12,color:{argb:DARK}};c.fill=fill(SOFT);c.alignment={vertical:"middle",wrapText:true,indent:1};ws.getRow(row).height=32;row++;
   const sessTotals=[];
   for(let i=0;i<N();i++){const tt=tplOf(W,i),ph=phaseFor(W,i);
    ws.mergeCells(row,1,row,NC);c=ws.getCell(row,1);c.value=`Тренировка ${i+1} · ${DAYL[tt]} — ${dateFor(W,i)}${ph?` · ${ph.name}, ${ph.day}-й день цикла`:""}`;c.font={name:F,bold:true,size:11,color:{argb:"FFFFFFFF"}};c.fill=fill("FF3B4E7A");c.alignment={vertical:"middle",indent:1};ws.getRow(row).height=22;row++;
    ws.mergeCells(row,1,row,NC);c=ws.getCell(row,1);c.value="Разминка: "+warmSteps(tt).map(x=>x[0]+" — "+x[1]).join(" · ")+(ph?`\nПо циклу: ${ph.adv}`:"");c.font={name:F,size:9,italic:true,color:{argb:"FF5B6474"}};c.fill=fill(WARM);c.alignment={wrapText:true,vertical:"middle",indent:1};ws.getRow(row).height=ph?44:30;row++;
    const first=row;
    for(let s=0;s<7;s++){const e=exRow(W,tt,s,i),sv=setVals(W,i,s,e).filter(Boolean),[lo,top]=lowUp(e.reps),key=tt+"|"+s;
     const mx=sv.length?Math.max(...sv.map(v=>v.w)):null,avg=sv.length?Math.round(sv.reduce((a,v)=>a+v.r,0)/sv.length):null;
     const orig=e.orig?`вместо «${e.orig}»`:"";
     const vals=[e.block,e.name,e.sets,dash(e.reps),e.rest,e.rpeTxt,e.rec||null,lo||null,mx,avg,null,orig||null,lo||0];
     vals.forEach((v,k)=>{const cc=ws.getCell(row,k+1);cc.value=v;cc.font={name:F,size:10,bold:k===1};cc.border=box;cc.alignment={vertical:"middle",wrapText:true,horizontal:[0,2,3,6,7,8,9,10].includes(k)?"center":"left"};});
     ws.getCell(row,12).font={name:F,size:9,color:{argb:"FF5B6474"}};
     const b=ws.getCell(row,1);b.fill=fill(roleCol[e.role]);b.font={name:F,bold:true,size:10,color:{argb:e.role==="Подсобное"?DARK:"FFFFFFFF"}};
     // Название: в 1-й встрече — ввод со списком, дальше — ссылка на неё (замена тянется на все недели).
     const nc=ws.getCell(row,2),swapped=e.name==="Спокойная ходьба или растяжка";
     if(!swapped){if(src[key]){const S0=src[key];nc.value={formula:(S0.m===m?"":`'Блок ${S0.m+1}'!`)+`B${S0.row}`,result:e.name};}else{src[key]={m,row};nc.fill=fill(INP);}if(!isHome())nc.dataValidation=dv(tt,s);}
     if(lo){[9,10].forEach(k=>{const cc=ws.getCell(row,k);cc.fill=fill(INP);cc.font={name:F,size:11,bold:true,color:{argb:"FF0000FF"}};});
      ws.getCell(row,10).dataValidation={type:"list",allowBlank:true,showErrorMessage:false,formulae:['"8,9,10,11,12,13,14,15"']};
      ws.getCell(row,11).value={formula:`IF(I${row}="","",C${row}*IF(J${row}="",M${row},J${row})*I${row})`};ws.getCell(row,11).numFmt="#,##0";ws.getCell(row,11).font={name:F,bold:true,size:10};
      const H=hist[key]||(hist[key]=[]),mu=e.ph?e.ph.mul:1,dl=w===3;
      let P=null;for(let k=H.length-1;k>=0;k--)if(!H[k].dl){P=H[k];break;}
      if(w===0&&W>=4){const B=H.find(x=>x.W===W-4);if(B)P=B;}
      if(P){const pre=P.m===m?"":`'Блок ${P.m+1}'!`,N0=`N${row}`,st=`IF(${N0}<20,1,2.5)`,rnd=x=>`IF(${x}<20,ROUND(${x},0),MROUND(${x},2.5))`;
       ws.getCell(row,14).value={formula:`IF(${pre}I${P.row}="","",${pre}I${P.row}/${P.mul})`};
       ws.getCell(row,15).value={formula:`IF(${pre}K${P.row}="","",${pre}K${P.row}/${P.mul})`};
       const cyc=mu<1&&!dl?` · по циклу вес −${Math.round((1-mu)*100)}%`:"";let x,reps,hint;
       if(dl){const k=Math.min(0.8,mu);x=`${N0}*${k}`;reps=`${lo}`;hint=`"Разгрузка: ${Math.round(k*100)}% от рабочего веса"`;}
       else if(top<P.top){x=`MAX(${N0}+${st},CEILING(${N0}*1.05,${st}))*${mu}`;reps=`${lo}`;hint=`"Повторов по плану меньше — вес +5%. Получается легко — делай до ${top}${cyc}"`;}
       else{const tg=`ROUND(O${row}*(1+${PADDR}),2)`,wp=`MAX(${N0},CEILING(${tg}/(C${row}*${top})-0.001,${st}))`;x=`${wp}*${mu}`;
        reps=`MAX(${lo},MIN(${top},ROUNDUP(${tg}/(C${row}*${wp})-0.001,0)))`;
        hint=`IF(${N0}*C${row}*${top}>=${tg},"Цель — тоннаж "&ROUND(${tg},0)&" кг: вес тот же, повторов больше${cyc}","Повторы были на максимуме — вес вверх${cyc}")`;}
       ws.getCell(row,7).value={formula:`IF(${N0}="",${e.rec||'""'},${rnd(x)})`};
       ws.getCell(row,8).value={formula:`IF(${N0}="",${lo},${reps})`};
       ws.getCell(row,12).value={formula:`IF(${N0}="",${orig?`"${orig}"`:'""'},${orig?`"${orig}. "&`:""}${hint})`};}
      H.push({W,m,row,mul:mu,top,dl});}
     else{[8,9,10,11].forEach(k=>{ws.getCell(row,k).value="—";ws.getCell(row,k).font={name:F,color:{argb:"FF9AA4B5"}};});}
     if(lo)[7,8].forEach(k=>{const cc=ws.getCell(row,k);cc.fill=fill("FFDCE8FF");cc.font={name:F,size:11,bold:true,color:{argb:"FF1A3FA8"}};});ws.getRow(row).height=e.orig?32:20;row++;}
    ws.mergeCells(row,1,row,10);c=ws.getCell(row,1);c.value="Тоннаж тренировки";c.font={name:F,bold:true,size:10};c.alignment={horizontal:"right"};
    c=ws.getCell(row,11);c.value={formula:`SUM(K${first}:K${row-1})`};c.numFmt="#,##0";c.font={name:F,bold:true};c.fill=fill("FFE2F3E7");c.border=box;sessTotals.push(`K${row}`);row+=2;}
   ws.mergeCells(row,1,row,10);c=ws.getCell(row,1);c.value=`ИТОГО НЕДЕЛЯ ${W+1}`;c.font={name:F,bold:true,size:11};c.alignment={horizontal:"right"};
   c=ws.getCell(row,11);c.value={formula:sessTotals.join("+")};c.numFmt="#,##0";c.font={name:F,bold:true,size:11};c.fill=fill(GREY);c.border=box;weekTotals.push(`K${row}`);row+=2;}
  ws.mergeCells(row,1,row,10);let c2=ws.getCell(row,1);c2.value=`ИТОГО БЛОК ${m+1}`;c2.font={name:F,bold:true,size:12};c2.alignment={horizontal:"right"};
  c2=ws.getCell(row,11);c2.value={formula:weekTotals.join("+")};c2.numFmt="#,##0";c2.font={name:F,bold:true,size:12};c2.fill=fill(SOFT);c2.border=box;}
 const xs=wb.addWorksheet("Упражнения",{views:[{state:"frozen",ySplit:2}]});xs.columns=PAT_ORDER.map(()=>({width:40}));
 xs.mergeCells(1,1,1,PAT_ORDER.length);xs.getCell(1,1).value="Списки для замены упражнений — в каждом от тяжёлого (свободный вес) к лёгкому (тренажёр, блок)";xs.getCell(1,1).font={name:F,bold:true,size:12};
 PAT_ORDER.forEach((p,k)=>{const c=xs.getCell(2,k+1);c.value=PAT_NAME[p];c.font={name:F,bold:true,color:{argb:"FFFFFFFF"}};c.fill=fill(ACC);c.alignment={wrapText:true,vertical:"middle"};c.border=box;
  lists[k].forEach((n,j)=>{const cc=xs.getCell(j+3,k+1);cc.value=n;cc.font={name:F,size:10};cc.border=box;});});
 xs.getRow(2).height=30;
 return await wb.xlsx.writeBuffer();}
const PAT_F=[["hip","knee","hip","hpull","vpull","hpush","core"],["hip","knee","hip","hpull","vpull","vpush","core"],["hip","knee","hip","hpull","vpull","hpush","core"]];
const patSet=()=>isF()?PAT_F:PAT;
const PAT=[["knee","hip","hpush","vpush","vpull","hpull","core"],["hpush","vpush","vpull","hpull","knee","hip","core"],["vpull","hpull","knee","hip","hpush","vpush","core"]];
const PAT_ORDER=["knee","hip","hpush","vpush","vpull","hpull","delts","core"];
const PAT_NAME={knee:"Ноги — коленодоминантные (приседы, выпады)",hip:"Ноги — тазодоминантные (тяги, мосты)",hpush:"Жимы лёжа (грудь)",vpush:"Жимы вверх (плечи)",vpull:"Тяги сверху (спина)",hpull:"Тяги к поясу (спина)",delts:"Плечи — изоляция",core:"Кор (пресс)"};
const PAT_LIST={
 knee:["Присед со штангой на спине","Фронтальный присед со штангой","Гоблет-присед","Гоблет-присед к скамье","Болгарские сплит-приседания с гантелями","Болгарские сплит-приседания","Шагающие выпады с гантелями","Выпады с гантелями","Выпады назад с гантелями","Выпады на месте","Зашагивания на коробку с гантелями","Зашагивания на платформу","Гакк-присед в тренажёре","Жим ногами","Разгибания ног в тренажёре"],
 hip:["Румынская тяга со штангой","Румынская тяга с гантелями","Ягодичный мост со штангой","Ягодичный мост с гантелью","Ягодичный мост в тренажёре","Гиперэкстензия со штангой","Гиперэкстензия на ягодицы","Гиперэкстензия","Сгибания ног в тренажёре","Отведение ноги назад в кроссовере","Отведение бедра в тренажёре","Мост на одной ноге"],
 hpush:["Жим штанги лёжа","Жим штанги на наклонной","Жим гантелей лёжа","Жим гантелей на наклонной","Жим гантелей лёжа нейтральным хватом","Отжимания на брусьях","Отжимания от пола","Жим в тренажёре от груди","Жим лёжа в тренажёре под 30°","Сведение рук в кроссовере","Бабочка (пек-дек)"],
 vpush:["Жим штанги стоя","Жим гантелей сидя","Жим гантелей сидя со спинкой","Жим Арнольда","Жим в рычаге (лэндмайн) одной рукой с колена","Жим в тренажёре сидя","Махи гантелями в стороны"],
 vpull:["Подтягивания с весом","Подтягивания","Подтягивания в гравитроне","Тяга верхнего блока широким хватом","Тяга верхнего блока","Тяга верхнего блока узким хватом","Тяга верхнего блока нейтральным хватом","Вертикальная тяга в рычажном тренажёре","Пуловер в блоке"],
 hpull:["Тяга штанги в наклоне","Тяга Т-грифа","Тяга гантели одной рукой","Тяга гантелей лёжа грудью на наклонной скамье","Тяга к поясу в рычажном тренажёре","Тяга в рычажном тренажёре с упором грудью","Тяга горизонтального блока","Тяга горизонтального блока"],
 delts:["Махи гантелями в стороны","Махи в стороны в блоке","Разведения гантелей в наклоне","Тяга каната к лицу","Махи в тренажёре"],
 core:["Ролик для пресса","Подъём ног в висе","Скручивания","Планка","Боковая планка","«Мёртвый жук»","Скручивание по Макгиллу","Паллоф-пресс в блоке","«Птица-собака»"]};

renderAll();

/* ---------- анкета-окно: по одному вопросу на экран ---------- */
function obSteps(){const st=[
 {k:"name",q:"Как тебя зовут?",t:"text"},
 {k:"sex",q:"Твой пол",t:"pick",opts:[["m","Мужской"],["f","Женский"]]},
 {k:"age",q:"Сколько тебе лет?",t:"num",min:14,max:90,unit:"лет"},
 {k:"height",q:"Твой рост",t:"num",min:120,max:230,unit:"см"},
 {k:"weight",q:"Твой вес сейчас",t:"num",min:35,max:250,unit:"кг",step:0.1},
 {k:"place",q:"Где будешь тренироваться?",t:"pick",opts:[["gym","В зале","Штанга, гантели, тренажёры"],["home","Дома, без инвентаря","Свой вес, по кругу, без прыжков"]]}];
 if(!isHome())st.push({k:"goal",q:"Какая у тебя цель?",t:"pick",opts:[["Жиросжигание","Похудеть","Убрать жир, сохранить мышцы"],["Набор массы","Набрать мышцы","Рост мышц, профицит калорий"],["Сила","Стать сильнее","Присед, жим, тяги — большие веса"]]});
 if(S.goal==="Жиросжигание")st.push({k:"fl",q:"Сколько хочешь сбросить?",t:"fl"});
 st.push(isHome()?{k:"level",q:"Как у тебя с нагрузкой?",t:"pick",opts:[["Новичок","Лёгкий","Только начинаю, давно не двигался"],["Средний","Средний","Немного двигаюсь, хочу бодрее"]]}:{k:"level",q:"Какой у тебя опыт в зале?",t:"pick",opts:[["Новичок","Новичок","Меньше 6 месяцев или после долгого перерыва"],["Средний","Средний","6 месяцев – 2 года, знаю технику базовых"],["Продвинутый","Продвинутый","Больше 2 лет, большие веса"]]},
  {k:"days",q:"В какие дни будешь тренироваться?",t:"days"},
  {k:"injs",q:"Есть травмы или ограничения?",t:"injs"});
 if($("sex").value==="f")st.push({k:"cyc",q:"Подстроить тренировки под цикл?",t:"cyc"});
 return st;}
let obI=0;
function obOpen(){obI=0;$("ob").hidden=false;document.body.style.overflow="hidden";obRender();}
function obClose(){$("ob").hidden=true;document.body.style.overflow="";}
const OB_SAY={place:"Можно и дома — я подстрою программу 🏠",name:"Привет! Я Гиря. Давай знакомиться 👋",sex:"Чтобы правильно посчитать калории",age:"Обещаю никому не говорить 🤫",height:"Выпрямись! Без обмана 😄",weight:"Это просто точка старта",goal:"Самый важный вопрос!",fl:"Не спеши — так результат останется",level:"Честно — от этого зависят веса",days:"Отдых между тренировками тоже работа",injs:"Береги себя — подберу упражнения",cyc:"Подстрою нагрузку под самочувствие"};
function kbJump(cls){const k=$("obKb");if(!k)return;k.classList.remove("jump","flex");void k.offsetWidth;k.classList.add(cls||"jump");}
function obRender(){const st=obSteps(),s=st[obI],b=$("obBody");$("obSay").textContent=OB_SAY[s.k]||"";
 $("obBar").style.width=((obI+1)/st.length*100)+"%";$("obBack").style.visibility=obI?"visible":"hidden";
 $("obNext").textContent=obI===st.length-1?"Собрать программу":"Далее";
 let h=`<h2 class="ob-q">${s.q}</h2>`;
 if(s.t==="text")h+=`<input class="ob-in" id="obV" type="text" value="${esc(touched?$("name").value:"")}" placeholder="Имя">`;
 if(s.t==="num")h+=`<div class="ob-num"><input class="ob-in" id="obV" type="number" inputmode="decimal" min="${s.min}" max="${s.max}" step="${s.step||1}" value="${esc(touched?$(s.k).value:"")}"><span>${s.unit}</span></div><p class="hint" id="obErr"></p>`;
 if(s.t==="pick"){const cur=s.k==="sex"?$("sex").value:S[s.k];h+=`<div class="ob-opts">${s.opts.map(o=>`<button type="button" class="ob-opt" data-v="${o[0]}" aria-pressed="${touched&&o[0]===cur}"><b>${o[1]}</b>${o[2]?`<span>${o[2]}</span>`:""}</button>`).join("")}</div>`;}
 if(s.t==="fl")h+=`<div class="ob-num"><input class="ob-in" id="obV" type="number" inputmode="decimal" min="1" max="80" step="0.5" value="${esc($("fl_kg").value)}"><span>кг</span></div><div class="ob-num"><input class="ob-in" id="obV2" type="number" inputmode="numeric" min="1" max="24" value="${esc($("fl_mo").value)}"><span>месяцев</span></div><p class="hint">Безопасно — до 1% веса в неделю.</p>`;
 if(s.t==="days")h+=`<div class="ob-days">${DAYN.map((d,i)=>`<button type="button" class="ob-day" data-d="${i}" aria-pressed="${S.days.includes(i)}">${d}</button>`).join("")}</div><p class="hint" id="obErr">Отметь 2–5 дней. Между тренировками лучше день отдыха.</p>`;
 if(s.t==="injs")h+=`<div class="ob-opts">${[["none","Нет, всё хорошо"],["lumbar","Поясница: грыжа, протрузия"],["knee","Колено"],["shoulder","Плечо"]].map(o=>`<button type="button" class="ob-opt" data-inj="${o[0]}" aria-pressed="${o[0]==="none"?!S.injs.length:S.injs.includes(o[0])}"><b>${o[1]}</b></button>`).join("")}</div><p class="hint">Можно выбрать несколько.</p>`;
 if(s.t==="cyc")h+=`<div class="ob-opts"><button type="button" class="ob-opt" data-cyc="1" aria-pressed="${$("cyc_on").checked}"><b>Да, учитывать</b><span>Веса и подходы подстроятся под фазу цикла</span></button><button type="button" class="ob-opt" data-cyc="0" aria-pressed="${!$("cyc_on").checked}"><b>Нет</b><span>Нерегулярный цикл, контрацептивы, беременность</span></button></div>
  <label class="f" id="obCycD" ${$("cyc_on").checked?"":"hidden"}>Первый день последних месячных<input class="ob-in" id="obV" type="date" value="${esc($("cyc_start").value)}"></label>`;
 b.innerHTML=h;const f=b.querySelector("input");if(f&&s.t!=="cyc")setTimeout(()=>f.focus(),50);}
async function obNext(){const st=obSteps(),s=st[obI],v=$("obV"),err=t=>{const e=$("obErr");if(e)e.textContent=t;else alert(t);};
 if(s.t==="text"){if(!v.value.trim())return;$("name").value=v.value.trim();}
 if(s.t==="num"){const x=parseFloat(String(v.value).replace(",","."));if(!(x>=s.min&&x<=s.max))return err(`Впиши число от ${s.min} до ${s.max}`);$(s.k).value=x;}
 if(s.t==="pick"&&!document.querySelector('#obBody .ob-opt[aria-pressed="true"]'))return;
 if(s.t==="fl"){const kg=parseFloat(v.value),mo=parseFloat($("obV2").value);if(!(kg>0&&mo>0))return;$("fl_kg").value=kg;$("fl_mo").value=mo;}
 if(s.t==="days"&&(S.days.length<2||S.days.length>5))return err("Нужно от 2 до 5 дней.");
 if(s.t==="cyc"&&$("cyc_on").checked){if(!v.value)return;$("cyc_start").value=v.value;}
 if(obI<st.length-1){obI++;obRender();kbJump();return;}
 kbJump("flex");$("obSay").textContent="Готово! Программа собрана 💪";await new Promise(r=>setTimeout(r,900));
 if(!touched)S.log={};touched=true;markTouched();S.W=0;renderAll();save();obClose();
 $("weekInfo").scrollIntoView({behavior:"smooth",block:"start"});}
document.body.insertAdjacentHTML("beforeend",`<div class="ob" id="ob" hidden role="dialog" aria-modal="true" aria-label="Анкета">
 <div class="ob-card"><div class="ob-prog"><i id="obBar"></i></div>
 <div class="ob-hero"><svg id="obKb" class="kb" viewBox="0 0 140 140" aria-hidden="true">
  <defs><radialGradient id="kbBody" cx="38%" cy="32%" r="75%"><stop offset="0" stop-color="#9DB8FF"/><stop offset=".55" stop-color="#5B86FF"/><stop offset="1" stop-color="#2F55D4"/></radialGradient>
   <linearGradient id="kbHandle" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#8FA9F5"/><stop offset="1" stop-color="#3B5BDB"/></linearGradient></defs>
  <ellipse cx="70" cy="132" rx="34" ry="5" fill="#000" opacity=".25"/>
  <ellipse cx="54" cy="125" rx="10" ry="6" fill="#2F55D4" stroke="#16224A" stroke-width="3.5"/><ellipse cx="86" cy="125" rx="10" ry="6" fill="#2F55D4" stroke="#16224A" stroke-width="3.5"/>
  <g class="kb-arm kb-arm-l"><path d="M34 86 Q18 80 15 64" fill="none" stroke="#16224A" stroke-width="11" stroke-linecap="round"/><path d="M34 86 Q18 80 15 64" fill="none" stroke="#5B86FF" stroke-width="5.5" stroke-linecap="round"/><circle cx="15" cy="60" r="9" fill="#FFD7B0" stroke="#16224A" stroke-width="3.5"/></g>
  <g class="kb-arm kb-arm-r"><path d="M106 86 Q122 80 125 64" fill="none" stroke="#16224A" stroke-width="11" stroke-linecap="round"/><path d="M106 86 Q122 80 125 64" fill="none" stroke="#5B86FF" stroke-width="5.5" stroke-linecap="round"/><circle cx="125" cy="60" r="9" fill="#FFD7B0" stroke="#16224A" stroke-width="3.5"/></g>
  <path d="M48 54 V34 Q48 14 70 14 Q92 14 92 34 V54" fill="none" stroke="#16224A" stroke-width="18" stroke-linecap="round"/>
  <path d="M48 54 V34 Q48 14 70 14 Q92 14 92 34 V54" fill="none" stroke="url(#kbHandle)" stroke-width="10" stroke-linecap="round"/>
  <path d="M55 30 Q58 22 66 20" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" opacity=".6"/>
  <circle cx="70" cy="86" r="42" fill="url(#kbBody)" stroke="#16224A" stroke-width="4"/>
  <path d="M30 72 Q70 56 110 72 L108 80 Q70 65 32 80 Z" fill="#FF5A5F" stroke="#16224A" stroke-width="3" stroke-linejoin="round"/>
  <path d="M106 74 l10 -6 l-2 9 z" fill="#FF5A5F" stroke="#16224A" stroke-width="2.5" stroke-linejoin="round"/>
  <ellipse cx="50" cy="66" rx="9" ry="4" fill="#fff" opacity=".35" transform="rotate(-20 50 66)"/>
  <path d="M48 84 Q55 80 62 84" fill="none" stroke="#16224A" stroke-width="3" stroke-linecap="round"/><path d="M78 84 Q85 80 92 84" fill="none" stroke="#16224A" stroke-width="3" stroke-linecap="round"/>
  <g class="kb-eyes"><ellipse cx="55" cy="95" rx="8.5" ry="10" fill="#fff" stroke="#16224A" stroke-width="3"/><ellipse cx="85" cy="95" rx="8.5" ry="10" fill="#fff" stroke="#16224A" stroke-width="3"/>
   <circle class="kb-pupil" cx="57" cy="97" r="5" fill="#16224A"/><circle class="kb-pupil" cx="87" cy="97" r="5" fill="#16224A"/><circle cx="59" cy="94.5" r="1.8" fill="#fff"/><circle cx="89" cy="94.5" r="1.8" fill="#fff"/></g>
  <ellipse cx="42" cy="108" rx="6" ry="3.5" fill="#FF8FA3" opacity=".75"/><ellipse cx="98" cy="108" rx="6" ry="3.5" fill="#FF8FA3" opacity=".75"/>
  <path class="kb-mouth" d="M60 108 Q70 122 80 108 Z" fill="#7A1F35" stroke="#16224A" stroke-width="3" stroke-linejoin="round"/>
  <path d="M64 113 Q70 120 76 113 Q70 111 64 113 Z" fill="#FF7A90"/>
 </svg><div class="ob-say" id="obSay"></div></div>
 <div id="obBody" class="ob-body"></div>
 <div class="ob-nav"><button type="button" class="ob-back" id="obBack">Назад</button><button type="button" class="ob-next" id="obNext">Далее</button></div>
 <button type="button" class="ob-skip" id="obSkip">Заполню сам в форме</button></div></div>`);
$("ob").addEventListener("click",ev=>{const b=ev.target.closest("button");if(!b)return;ev.stopPropagation();
 const s=obSteps()[obI];
 if(b.id==="obNext")return obNext();
 if(b.id==="obBack"){if(obI>0){obI--;obRender();}return;}
 if(b.id==="obSkip"){touched=true;markTouched();save();obClose();return;}
 if(b.classList.contains("ob-opt")&&s.t==="pick"){if(s.k==="sex")$("sex").value=b.dataset.v;else S[s.k]=b.dataset.v;if(s.k==="goal")S.pace=1;if(s.k==="place"&&S.place==="home"){S.goal="Жиросжигание";if(S.level==="Продвинутый")S.level="Средний";}
  b.parentElement.querySelectorAll(".ob-opt").forEach(x=>x.setAttribute("aria-pressed",x===b));$("obSay").textContent=["Отличный выбор!","Принято 👍","Записал!"][obI%3];kbJump();return;}
 if(b.dataset.d!=null){const d=+b.dataset.d;if(S.days.includes(d))S.days=S.days.filter(x=>x!==d);else if(S.days.length<5)S.days=[...S.days,d].sort((a,c)=>a-c);obRender();return;}
 if(b.dataset.inj){const k=b.dataset.inj;S.injs=k==="none"?[]:S.injs.includes(k)?S.injs.filter(x=>x!==k):[...S.injs,k];obRender();return;}
 if(b.dataset.cyc){$("cyc_on").checked=b.dataset.cyc==="1";obRender();return;}});
$("ob").addEventListener("keydown",ev=>{if(ev.key==="Enter"&&ev.target.tagName==="INPUT"){ev.preventDefault();obNext();}});
$("obAgain").addEventListener("click",obOpen);
if(!touched)obOpen();

})();
</script>
</body></html>
@endverbatim
