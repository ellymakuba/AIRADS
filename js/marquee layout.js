//	     -- JavaScript Marquee v3.0 --
//
//        JavaScript Copyright (c) Tarmle 2003
//
//	Visit ruinsofmorning.net for full 
//	instructions on installing and running
//	this script on your web pages!
//
// You may use this code on the condition that you
// leave this message intact. Cheers.
//
//  - Tarmle


// ----------- SETTINGS ----------- //

// Messages - There MUST be AT LEAST two messages. ALL apostrophes (') MUST be escaped using a backslash (\').
var msgarray=new Array
(
  'Message 1','Message 2','Message 3','Message 4'
);

// Speed Settings //
var pausetime = 1000;		// Pause Length (milliseconds).
var msginc = 30;		// The number of increments for each transition (lower = faster). 
var interval = 50;		// Interval between movment steps (milliseconds - less is faster & smoother).
var ppat = new Array();		// Pause pattern. List pause lengths for each message in milliseconds, eg (2500,0,1000).
var ipat = new Array();		// Increment pattern. List the number of steps for the transition of each message, eg (20,1,40).
var wraptime = false;		// Prevent resetting speed patterns.

// Direction Settings //
var direction = 'rand';		// Direction: 'up', 'down', 'left', 'right', and combos such as 'downleftright', 'rand', 'prand', 'xrand', 'pattern', 'dpattern', 'wpattern' (see below).
var dpat = new Array();		// For direction setting 'pattern', 'prand' and 'xrand' only.

// Transition Settings //
var transition = 'rand';	// Transition method: 'contig', 'cover', 'uncover', 'wipe', 'unwipe', 'coverwipe', 'uncoverwipe', 'rand', 'prand', 'xrand', 'pattern', 'dpattern', 'wpattern' (see below).
var tpat = new Array();		// For transition setting 'pattern', 'dpattern', 'wpattern', 'prand' and 'xrand' only.

// Colour Settings //
var bgcolor = 'pattern';	// Background colour of the marquee and messages eg '#0099FF', 'white' or 'images/myimage.jpg'.
var cpat = new Array('#eee','white');	// Background colour pattern: List the background colour for each maessage.

// Advanced //
var mhalign = 'center';		// Horizontal alignment setting for TD container.
var mvalign = 'middle';		// Vertical alignment setting for TD container.
var csspat = new Array();	// List CSS Classes for each message TD container - default 'mrqtd' (TD.mrqtd).

// Opera Position Adjustment - set these two variables to match CSS margins (if used - otherwise 5 is normal). //
var dispv = 5;			// Top margin in pixels.
var disph = 5;			// Left margin in pixels.

// Bottom-Right Netscape Saftey //
var nsafe = false;		// Evade direction/transition combos that cause scrollbar pop-up in NS.

// Message Dump //
mdump = true;
dumptest = false;

//----------- DO NOT EDIT BELOW THIS LINE! -----------//


var appN = navigator.appName; var appV = navigator.appVersion.substring(0,1);
var ie = (appN=="Microsoft Internet Explorer" && appV >= 4) ? true : false;
var ns = (appN=="Netscape" && (appV >= 4 && appV < 5)) ? true : false;
var nsix = (appN=="Netscape" && appV >= 5) ? true : false;
var opsev = (navigator.userAgent.indexOf('Opera 7.') >= 0 || navigator.userAgent.indexOf('Opera/7') >= 0) ? true : false;

if (opsev) {bgcolor=(bgcolor=='transparent')?'':bgcolor;} else {dispv = 0; disph = 0;}
if (ns||nsix) {bgcolor=(bgcolor=='transparent')?'':bgcolor;}

if (dumptest) {ie=0;ns=0;nsix=0;opsev=0;}

mc=0;mcp=1;msgn=msgarray.length; msginc-=0.1;
direc='';trans='';
dpatc=0;tpatc=0;ppatc=0;ipatc=0;
d=document;
pflag=false;rflag=false;oflag=false;

mrqh=null;mrqw=null;mrqt=null;mrql=null;
mh=null;mw=null;mt=null;ml=null;
du=0;dr=0;dd=0;dl=0;
vt=0;vl=0;IID=0;TID=0;RID=0;NTID=0;

skipar=new Array(); skipcount = 0;
tar = new Array('contig','cover','uncover','wipe','unwipe');
dar = new Array('up','down','left','right','updown','upleft','upright','downleft','downright','leftright','leftrightdown','leftrightup','updownleft','updownright','updownleftright');

if (transition == 'xrand')
{
 tempa = new Array();
 for (cb=0; cb < tar.length; cb++)
 {
  block = false;
  for (ca=0; ca < tpat.length; ca++) {if (tpat[ca] == tar[cb]) {block = true;}}
  if (!block) {tempa.push(tar[cb]);}
 }
 tar = tempa; transition = 'rand';
} else if (transition == 'prand') {tar = tpat; transition = 'rand';
} else if (transition == 'dpattern') {tempa = Array();for (c=0; c<tpat.length; c++) {tempa.push(tpat[c],tpat[c]);}tpat=tempa;transition='pattern'}

if (direction == 'xrand')
{
 tempa = new Array();
 for (cb=0; cb < dar.length; cb++)
 {
  block = false;
  for (ca=0; ca < dpat.length; ca++) {if (dpat[ca] == dar[cb]) {block = true;}}
  if (!block) {tempa.push(dar[cb]);}
 }
 dar = tempa; direction = 'rand';
} else if (direction == 'prand') {dar = dpat; direction = 'rand';
} else if (direction == 'dpattern') {tempa = Array();for (c=0; c<dpat.length; c++) {tempa.push(dpat[c],dpat[c]);}dpat=tempa;direction='pattern'}


function beginmrq()
{
 if (ie||ns||nsix||opsev)
 {
  setupmrq();
  laymrq();
  laymsg();
  clearInterval(IID);
  pause();
 }
}


function insertdivs()
{
 c=0;mrqins='';msgins='';
 if (!(ie||ns||nsix||opsev)&&!mdump) {return;}
 setupmrq();
 if (ns)  mrqins=' name="marquee"';
 d.write('<div id="marquee"'+mrqins+' onmouseover="javascript:clientpause(true);" onmouseout="javascript:clientpause(false);" class="marquee">');
 for (i=0; i<msgarray.length; i++)
 {
  if (msgarray[i].indexOf('<!--skip-->')>=0) {skipar[i]='skip'; skipcount++;} else {skipar[i]='noskip';}
  if (ns) msgins=' name="message'+i+'"';
  bc=''; bi='';
  cssins = (csspat.length) ? csspat[i%csspat.length] : 'mrqtd';
  if (bgcolor == 'pattern') {bc=cpat[i%cpat.length];} else {bc=bgcolor;}
  if (bc.indexOf('.')>0) {bi='background: url('+bc+');';} else {bi='background:'+bc+';';}
  startmsg='<div id="message'+i+'"'+msgins+' class="message"><table width="'+mrqw+'" height="'+mrqh+'" border="0" cellspacing="0" cellpadding="0" style="'+bi+'"><tr><td height="'+mrqh+'" width="'+mrqw+'" align="'+mhalign+'" valign="'+mvalign+'" class="'+cssins+'">';
  d.write(startmsg+msgarray[i]+'</td></tr><tr><td><img src="spacer.gif" width="'+mrqw+'" height="1" alt=""></td></tr></table></div>');
 }
 d.write('</div>');
 if (!(ie||ns||nsix||opsev)&&mdump) {return;} else {beginmrq();}
}


function setupmrq()
{
 if (ie||nsix||opsev)
 {
  mspace=d.mrqspacer;
  mrqt=mspace.offsetTop+dispv; mrql=mspace.offsetLeft+disph;
  mrqw=mspace.width; mrqh=mspace.height;
 } else {
  mspace=d.images.mrqspacer;
  mrqt=mspace.y+dispv; mrql=mspace.x+disph;
  mrqw=mspace.width; mrqh=mspace.height;
 }
 mw=mrqw; mh=mrqh; mt=mrqh;
}

function laymrq()
{
 if (ie||nsix||opsev)
 {
  with (d.getElementById('marquee').style)
  {
   top=mrqt+'px'; left=mrql+'px'; width=mrqw+'px'; height=mrqh+'px';
   clip="rect(0px "+mrqw+"px "+mrqh+"px 0px)";
   visibility='visible';
  };
 } else {
  with (d.layers.marquee)
  {
   top=mrqt; left=mrql; width=mrqw; height=mrqh;
   clip.width=mrqw; clip.height=mrqh;
   visibility="show";
  };
 }
}


function laymsg()
{
 for (mc=0; mc < msgn; mc++)
 {
  if (ie||nsix||opsev)
  {
   with (d.getElementById("message"+mc).style)
   {
    width=mw+'px'; height=mh+'px';
    if (mc) {top=-1000+'px'; left=-1000+'px';} else {top=0+'px'; left=0+'px';}
    vt=0;
    clip="rect(0px "+mrqw+"px "+mrqh+"px 0px)";
    visibility="visible";
   }
  } else {
   with (d.layers.marquee.document["message"+mc])
   {
    if (mc) {top=-1000; left=-1000;} else {top=0; left=0;}
    vt=0;
    clip.width=mw; clip.height=mh;
    visibility="show";
   }
  }
 }
 mc=0;
}


function timing() {clearInterval(IID); IID=setInterval("pflag=false;movemsg();if(pflag)pause();",interval);}

function pause()
{
 clearInterval(IID);
 laymrq();

 if (ppat.length)
 {
  if(wraptime){ptime=ppat[ppatc%ppat.length];}else{ptime=ppat[mc%ppat.length];}
 }else{ptime=pausetime;}

 if (ipat.length)
 {
  if(wraptime){msginc=ipat[ipatc%ipat.length];}else{msginc=ipat[mc%ipat.length];msginc-=0.1}
 }
 if(msginc<0.9)msginc=0.9;

 if(direction=='rand'){rn=Math.round(Math.random()*(dar.length-1));direc=dar[rn];}
 else if(direction=='pattern'){direc=dpat[mc%dpat.length];}
 else if(direction=='wpattern'){direc=dpat[dpatc%dpat.length];}
 else{direc=direction;}

 if(transition=='rand'){rn=Math.round(Math.random()*(tar.length-1));trans=tar[rn];}
 else if(transition=='pattern'){trans=tpat[mc%tpat.length];}
 else if(transition=='wpattern'){trans=tpat[tpatc%tpat.length];}
 else{trans=transition;}

 du=0;dr=0;dd=0;dl=0;
 du=(direc.indexOf('up')>=0)?1:0;
 dl=(direc.indexOf('left')>=0)?1:0;
 dd=(direc.indexOf('down')>=0)?1:0;
 dr=(direc.indexOf('right')>=0)?1:0;
 if(((du+dd>1)||(dl+dr>1))&&trans!='unwipe'&&trans!='wipe'){trans=(Math.round(Math.random()*1))?'wipe':'unwipe';}
 if(nsafe&&(ns||nsix))
 {
  if(trans=='contig') {trans='wipe';}
  if(trans=='cover'||trans=='coverwipe'){if(du)du=0,dd=1;if(dl)dl=0,dr=1;}
  if(trans=='uncover'||trans=='uncoverwipe'){if(dd)dd=0,du=1;if(dr)dr=0,dl=1;}
 }
 if ((trans=='coverwipe'||trans=='uncoverwipe')&&(du+dd+dl+dr)>1){trans=(trans='coverwipe')?'cover':'uncover';}
 TID=setTimeout("clearTimeout(TID);timing()",ptime);
}

function repos()
{
 rflag=true;
 clearInterval(IID);
 clearTimeout(TID);
 if (ie||nsix||opsev)
 {
  RID=setTimeout("clearTimeout(RID);clearTimeout(TID);clearInterval(IID);setupmrq();laymrq();timing();rflag=false;if(oflag)clientpause(1)",1500);
 } else if (ns) {
  window.location.reload();
 }
}


function st(mn,pos)
{
 if (ie||nsix||opsev) {d.getElementById("message"+mn).style.top=pos+'px';} else {d.layers.marquee.document["message"+mn].top=pos;}
}

function sl(mn,pos)
{
 if (ie||nsix||opsev) {d.getElementById("message"+mn).style.left=pos+'px';} else {d.layers.marquee.document["message"+mn].left=pos;}
}

function sc(m,t,r,b,l)
{
 if (ie||nsix||opsev)
 {
  d.getElementById("message"+m).style.clip="rect("+t+"px "+r+"px "+b+"px "+l+"px)";
 } else {
  with (d.layers.marquee.document["message"+m]) {clip.top=t; clip.left=l; clip.width=l-r; clip.height=b-t;}
 }
}

function sz(mn,zin)
{
 if (ie||nsix||opsev) {d.getElementById("message"+mn).style.zIndex=zin;} else {d.layers.marquee.document['message'+mn].zIndex=zin;}
}

function sv(mn,tf)
{
 if (ie||nsix||opsev) {d.getElementById("message"+mn).style.visibility = (tf) ? 'visible' : 'hidden';} else {d.layers.marquee.document['message'+mn].visibility = (tf) ? 'show' : 'hide';}
}


function movemsg()
{
 if (du) {vt -= (mh/msginc);}
 if (dl) {vl -= (mw/msginc);}
 if (dd&&!du) {vt += (mh/msginc);}
 if (dr&&!dl) {vl += (mw/msginc);}

 sv(mc,true);

 if (trans == 'contig' || trans == 'uncover' || trans=='uncoverwipe')
 {
  st(mc,vt); sl(mc,vl);
  if (du) {st(mc,vt);}
  if (dl) {sl(mc,vl);}
  if (dd) {st(mc,vt);}
  if (dr) {sl(mc,vl);}
 } else if (trans=='coverwipe' && !((dr||dl)&&(du||dd))) {
  at=0; ar=mw; ab=mh; al=0;
  sl(mc,0); st(mc,0);
  if (du) {ab=mh+vt;}
  if (dl) {ar=mw+vl}
  if (dd) {at=vt}
  if (dr) {al=vl}
  sc(mc,at,ar,ab,al);
 } else {
  sl(mc,0); st(mc,0);
 }

 mcp=mc+1;
 if (mcp >= msgn) {mcp=0;}

 if (trans == 'uncover' || trans == 'wipe') {sz(mc,2);} else {sz(mc,0);}

 if (trans == 'wipe' || trans == 'unwipe')
 {
  st(mc,0); sl(mc,0);
  st(mcp,0); sl(mcp,0);
  at=0; ar=mw; ab=mh; al=0; bt=0; br=mw; bb=mh; bl=0;
  if (du) {ab=vt+mh; bt=vt+mh;}
  if (dl) {ar=vl+mw; bl=vl+mw;}
  if (dd) {at=vt; bb=vt;}
  if (dr) {al=vl; br=vl;}
  if (trans == 'wipe')
  {
   if (dl&&dr) {ar-=(vl/2); al=-(vl/2); br=mw; bl=0;}
   if (du&&dd) {at=-(vt/2); ab-=(vt/2); bt=0; bb=mh;}
   if ((du||dd)&&(dl||dr)) {bt=0; br=mw; bb=mh; bl=0;}
  } else {
   if (dl&&dr) {br=(mw/2)-(vl/2); bl=(mw/2)+(vl/2); ar=mw; al=0;}
   if (du&&dd) {bt=(mh/2)+(vt/2); bb=(mh/2)-(vt/2); at=0; ab=mh;}
   if ((du||dd)&&(dl||dr)) {at=0; ar=mw; ab=mh; al=0;}
  }
  at=(at<0)?0:at; ab=(ab>mh)?mh:ab; al=(al<0)?0:al; ar=(ar>mw)?mw:ar;
  bt=(bt<0)?0:bt; bb=(bb>mh)?mh:bb; bl=(bl<0)?0:bl; br=(br>mw)?mw:br;

  sc(mc,at,ar,ab,al);
  sc(mcp,bt,br,bb,bl);
 }

 sv(mcp,true);

 if (trans == 'contig' || trans == 'cover' || trans=='coverwipe')
 {
  st(mcp,vt); sl(mcp,vl);
  if (du) {st(mcp,vt+mh);}
  if (dl) {sl(mcp,vl+mw);}
  if (dd) {st(mcp,vt-mh);}
  if (dr) {sl(mcp,vl-mw);}
 } else if (trans=='uncoverwipe' && !((dr||dl)&&(du||dd))) {
  bt=0; br=mw; bb=mh; bl=0;
  sl(mcp,0); st(mcp,0);
  if (du) {bt=mh+vt;}
  if (dl) {bl=mw+vl}
  if (dd) {bb=vt}
  if (dr) {br=vl}
  sc(mcp,bt,br,bb,bl);
 } else {
  sl(mcp,0); st(mcp,0);
 }

 if (trans == 'uncover' || trans == 'wipe') {sz(mcp,0);} else {sz(mcp,2);}

 stepf = false;

 if (du && vt <= 0-mh) {stepf = true;}
 if (dd && vt >= mh) {stepf = true;}
 if (dl && vl <= 0-mw) {stepf = true;}
 if (dr && vl >= mw) {stepf = true;}

 if (stepf)
 {
  sv(mc,false);
  st(mc,-1000);
  sl(mc,-1000);
  sz(mc,0);
  sc(mc,0,mw,mh,0);
  vl = 0; vt = 0;
  mc++;
  if (mc >= msgn) mc=0;
  mcp=mc+1;
  if (mcp >= msgn) mcp=0;
  sl(mc,vl); st(mc,vt);
  sc(mc,0,mw,mh,0);
  dpatc++; tpatc++; ipatc++; ppatc++;
  dpatc=(dpatc%dpat.length)?dpatc:0;
  tpatc=(tpatc%tpat.length)?tpatc:0;
  ipatc=(ipatc%ipat.length)?ipatc:0;
  ppatc=(ppatc%ppat.length)?ppatc:0;
  pflag=true;
 }
}


function clientpause(tf)
{
 if (!(ie||ns||nsix||opsev)) return;
 clearInterval(IID); clearTimeout(TID);
 if (tf) {oflag=true;} else {oflag=false; pause(); return;}
 vl = 0; vt = 0;
 sc(mc,0,mw,mh,0);
 sc(mcp,0,mw,mh,0);
 sl(mc,vl); st(mc,vt); sz(mc,2);
 sl(mcp,-1000); st(mcp,-1000); sz(mcp,0);
}


function clientstep(stepd)
{
 if (!(ie||ns||nsix||opsev)) return;
 skipper = true;
 while(skipar[mc] == 'skip' || skipper)
 {
  skipper = false;
  if (stepd == 'back') {mc--; pnstep=-1;} else {mc++; pnstep=1;}
  if (mc >= msgn) mc = 0; if (mc < 0) mc = msgn-1;
  mcp = mc+1;
  if (mcp >= msgn) mcp = 0; if (mcp < 0) mcp = msgn-1;
  for (cz=0; cz<msgn; cz++) {sl(cz,-1000); st(cz,-1000); sz(cz,0); sv(mc,false)};
  vl=0;vt=0;
  sl(mc,vl); st(mc,vt); sz(mc,2); sv(mc,true);
  dpatc+=pnstep; tpatc+=pnstep; ipatc+=pnstep; ppatc+=pnstep;
  if (dpatc>=dpat.length) dpatc=0; if (dpatc<0) dpatc=dpat.length-1;
  if (tpatc>=tpat.length) tpatc=0; if (tpatc<0) tpatc=tpat.length-1;
  if (ppatc>=ppat.length) ppatc=0; if (ppatc<0) ppatc=ppat.length-1;
  if (ipatc>=ipat.length) ipatc=0; if (ipatc<0) ipatc=ipat.length-1;
  if (skipcount>=msgn) break;
 }
}

if (ie||ns||nsix||opsev) {document.write('<style><!-- .marquee,.message {visibility: hidden; position: absolute; z-index: 1; overflow:hidden; background-color: transparent; layer-background-color: transparent;} --></style>');}

function rerepos(){repos(); NTID=setTimeout("clearTimeout(NTID);repos();",500);}
if (ns||nsix) {window.onload=rerepos; window.onresize=rerepos;} else {window.onload=repos; window.onresize=repos;}