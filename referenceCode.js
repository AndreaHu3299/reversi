<script language="javascript" 
type="text/javascript">
<!-- 

var num_cells;
var cells;
var candidates;
var num_candidates;
var num_lines;
var num_columns;
var totals;
var options;
var capture;
var capture_len;
var phase;
var tmp_cells;
var tmp_totals;
var tmp_options;
var tmp_phase;
var mode;
var timerID;
var timeout;
var hlight_count;
var hlight_x;
var hlight_y;
var hlight_phase;
var state;
var game_ended;

rnd.today=new Date(); rnd.seed=rnd.today.getTime();

function Init_()
{
var i,n;

for (i=0; i<num_cells; i++)
{
n = "Cell" + i;
cells[i] = parseInt(document.getElementById(n).value);
}

totals[0]=60;
totals[1]=2;
totals[2]=2;

options[0]=0;
options[1]=4;
options[2]=4;

phase=1;
state=0;
game_ended=0;

SaveCells();
Redraw();
RedrawVars();
}

function Init()
{
var i,n;

cells = new Array();
tmp_cells = new Array();
totals = new Array();
tmp_totals = new Array();
options = new Array();
tmp_options = new Array();
capture = new Array();
candidates = new Array();
num_candidates=0;

capture_len=0;
mode=1;

num_cells = parseInt(document.getElementById('NumCells').value);
num_lines = parseInt(document.getElementById('NumLines').value);
num_columns = parseInt(document.getElementById('NumColumns').value);

for (i=0; i<num_cells; i++)
{
n = "Cell" + i;
cells[i] = parseInt(document.getElementById(n).value);
}

totals[0]=60;
totals[1]=2;
totals[2]=2;

options[0]=0;
options[1]=4;
options[2]=4;

phase=1;
state=0;
game_ended=0;

SaveCells();
Redraw();
RedrawVars();
}

function HighlightSelection()
{
var n,count=0;
timeout=500;

n = 'td' + '-' + hlight_x + '-' + hlight_y;
if ( hlight_count > 0 )
{
timerID  = setTimeout("HighlightSelection()", timeout);
if (hlight_count<4)
{
 if (hlight_count%2)
 {
   document.getElementById(n).className="cellover"+hlight_phase;
 }
 else
 {
   document.getElementById(n).className="cellout";
   //state=0;
 }
}
hlight_count--;
}
else
{
clearTimeout(timerID);
timerID=0;
document.getElementById(n).className="cellout";

count = InvertSquare(hlight_x,hlight_y,2);
if ( count > 0 )
{
 RecomputeVars(hlight_x,hlight_y,count);
 Redraw();
 RedrawVars();
 state=0;
}
}
}

function Redraw_()
{
var i,n,x,y;
var fldId;
var Fld;
var img;

for (i=0; i<num_cells; i++)
{
n = "Cell" + i;
x = i + num_columns;
y = Math.floor(i - num_columns);
fldId='I'+x+'-'+y;
Fld = document.getElementById(fldId);
if ( cells[i] >= 0 )
{
 img='images/image_'+cells[i]+'.jpg';
}
else 
{
 img='images/base.jpg';
}
Fld.src=img;
}
}


function Redraw()
{
var i,n,x,y;
var fldId;
var Fld;
var img;

for (i=0; i<num_cells; i++)
{
n = "Cell" + i;
x = i % num_columns;
y = Math.floor(i / num_columns);
fldId='I'+x+'-'+y;
Fld = document.getElementById(fldId);
if ( cells[i] > 0 )
{
 img='images/image_'+cells[i]+'.png';
}
else 
{
 img='images/base.png';
}
Fld.src=img;
}
}


function RedrawVars()
{
var img;

img='images/hand_'+phase+'.png';

document.getElementById("CurrPlay").innerHTML = "<img src='"+img+"'/>";
if (phase==1)
{
document.getElementById("TurnId").innerHTML = "&nbsp;&nbsp;&nbsp;Your turn&nbsp;&nbsp;&nbsp;";
document.getElementById("a2").className = "ClassTotal2";
document.getElementById("a1").className = "ClassTurn1";
}
else
{
document.getElementById("TurnId").innerHTML = "Computer turn";
document.getElementById("a1").className = "ClassTotal1";
document.getElementById("a2").className = "ClassTurn2";
}
document.getElementById("score1").innerHTML = totals[1];
document.getElementById("score2").innerHTML = totals[2];

/*
document.getElementById("options1").innerHTML = options[1];
document.getElementById("options2").innerHTML = options[2];
*/
}

function SaveCells()
{
var i;

for (i=0; i<num_cells; i++)
{
tmp_cells[i] = cells[i];
}

tmp_totals[0]=totals[0];
tmp_totals[1]=totals[1];
tmp_totals[2]=totals[2];

tmp_options[0] = options[0];
tmp_options[1] = options[1];
tmp_options[2] = options[2];

tmp_phase = phase;
}

function RestoreCells()
{
var i;

for (i=0; i<num_cells; i++)
{
cells[i] = tmp_cells[i];
}

totals[0]=tmp_totals[0];
totals[1]=tmp_totals[1];
totals[2]=tmp_totals[2];

options[0] = tmp_options[0];
options[1] = tmp_options[1];
options[2] = tmp_options[2];

phase = tmp_phase;
}

function PassTurn()
{
if ( game_ended== 1 ) return;
var img;
if ( phase == 1 ) phase = 2;
else if ( phase == 2 ) phase = 1;

img='images/hand_'+phase+'.png';
document.getElementById("CurrPlay").innerHTML = "<img src='"+img+"'/>";
if ( phase == 2 )
{
PcPlay();
}
document.getElementById("Pass").disabled = true;
document.getElementById("StatusId").value = "";
state=0;
}

function HandleSquare(x,y)
{
if ( game_ended== 1 ) return;
if ( state == 1 ) return;
//window.status="x="+x+" y="+y;

var count=0;

SaveCells();

count = InvertSquare(x,y,phase);
if ( count > 0 )
{
state=1;
UnSelect(x,y);
RecomputeVars(x,y,count);
Redraw();
RedrawVars();
if ( mode==1)
{
 PcPlay();
}
else
{
 state=0;
}
}
}

function rnd() 
{
rnd.seed = (rnd.seed*9301+49297) % 233280;
return rnd.seed/(233280.0);
}

function rand(modulo) 
{
return Math.ceil(rnd()*modulo)-1;
}

function PcPlay()
{
if ( game_ended== 1 ) return;
var i,x,y;
var cnt = BuildCandidates(2);

if (cnt>0)
{
i=Analize(cnt);
num_candidates=0;
x = i % num_columns;
y = Math.floor(i / num_columns);

hlight_count=4;
hlight_x=x;
hlight_y=y;
hlight_phase=2;
HighlightSelection();
}
}

function Analize(cnt)
{
var i,r,ind=-1;

for (i=0; i<num_candidates; i++)
{
if ( ( candidates[i] == 0 ) ||
    ( candidates[i] == 7 ) ||
    ( candidates[i] == 56 ) ||
    ( candidates[i] == 63 ) )
{
 ind = candidates[i];
 break;
}
}

if ( ind != -1 )
{
return ind;
}

r = rand(cnt);

if ( ( candidates[r] != 9 ) && 
  ( candidates[r] != 14 ) && 
  ( candidates[r] != 49 ) &&
  ( candidates[r] != 54 ) &&
  ( candidates[r] != 1 ) &&
  ( candidates[r] != 8 ) &&
  ( candidates[r] != 6 ) &&
  ( candidates[r] != 15 ) &&
  ( candidates[r] != 48 ) &&
  ( candidates[r] != 57 ) &&
  ( candidates[r] != 55 ) &&
  ( candidates[r] != 62 ) )
{
return (candidates[r]);
}

if ( ( candidates[r] == 9 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 14 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 49 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 54 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   

if ( ( candidates[r] == 1 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 8 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 6 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 15 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 48 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 57 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 55 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 62 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   

r = rand(cnt);

if ( ( candidates[r] != 9 ) && 
  ( candidates[r] != 14 ) && 
  ( candidates[r] != 49 ) &&
  ( candidates[r] != 54 ) &&
  ( candidates[r] != 1 ) &&
  ( candidates[r] != 8 ) &&
  ( candidates[r] != 6 ) &&
  ( candidates[r] != 15 ) &&
  ( candidates[r] != 48 ) &&
  ( candidates[r] != 57 ) &&
  ( candidates[r] != 55 ) &&
  ( candidates[r] != 62 ) )
{
return (candidates[r]);
}

if ( ( candidates[r] == 9 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 14 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 49 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 54 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   

if ( ( candidates[r] == 1 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 8 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 6 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 15 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 48 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 57 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 55 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 62 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   

r = rand(cnt);

if ( ( candidates[r] != 9 ) && 
  ( candidates[r] != 14 ) && 
  ( candidates[r] != 49 ) &&
  ( candidates[r] != 54 ) &&
  ( candidates[r] != 1 ) &&
  ( candidates[r] != 8 ) &&
  ( candidates[r] != 6 ) &&
  ( candidates[r] != 15 ) &&
  ( candidates[r] != 48 ) &&
  ( candidates[r] != 57 ) &&
  ( candidates[r] != 55 ) &&
  ( candidates[r] != 62 ) )
{
return (candidates[r]);
}

if ( ( candidates[r] == 9 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 14 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 49 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 54 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   

if ( ( candidates[r] == 1 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 8 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 6 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 15 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 48 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 57 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 55 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 62 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   

r = rand(cnt);

if ( ( candidates[r] != 9 ) && 
  ( candidates[r] != 14 ) && 
  ( candidates[r] != 49 ) &&
  ( candidates[r] != 54 ) &&
  ( candidates[r] != 1 ) &&
  ( candidates[r] != 8 ) &&
  ( candidates[r] != 6 ) &&
  ( candidates[r] != 15 ) &&
  ( candidates[r] != 48 ) &&
  ( candidates[r] != 57 ) &&
  ( candidates[r] != 55 ) &&
  ( candidates[r] != 62 ) )
{
return (candidates[r]);
}

if ( ( candidates[r] == 9 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 14 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 49 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 54 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   

if ( ( candidates[r] == 1 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 8 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 6 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 15 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 48 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 57 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 55 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 62 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   

r = rand(cnt);

if ( ( candidates[r] != 9 ) && 
  ( candidates[r] != 14 ) && 
  ( candidates[r] != 49 ) &&
  ( candidates[r] != 54 ) &&
  ( candidates[r] != 1 ) &&
  ( candidates[r] != 8 ) &&
  ( candidates[r] != 6 ) &&
  ( candidates[r] != 15 ) &&
  ( candidates[r] != 48 ) &&
  ( candidates[r] != 57 ) &&
  ( candidates[r] != 55 ) &&
  ( candidates[r] != 62 ) )
{
return (candidates[r]);
}

if ( ( candidates[r] == 9 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 14 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 49 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 54 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   

if ( ( candidates[r] == 1 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 8 ) && (cells[0]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 6 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 15 ) && (cells[7]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 48 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 57 ) && (cells[56]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 55 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 62 ) && (cells[63]==2) )
{
return (candidates[r]);
}                   

r = rand(cnt);

if ( ( candidates[r] != 9 ) && 
  ( candidates[r] != 14 ) && 
  ( candidates[r] != 49 ) &&
  ( candidates[r] != 54 ) &&
  ( candidates[r] != 1 ) &&
  ( candidates[r] != 8 ) &&
  ( candidates[r] != 6 ) &&
  ( candidates[r] != 15 ) &&
  ( candidates[r] != 48 ) &&
  ( candidates[r] != 57 ) &&
  ( candidates[r] != 55 ) &&
  ( candidates[r] != 62 ) )
{
return (candidates[r]);
}

for (i=0; i<num_candidates; i++)
{
if ( ( candidates[i] != 9 ) && 
    ( candidates[i] != 14 ) && 
    ( candidates[i] != 49 ) &&
    ( candidates[i] != 54 ) &&
    ( candidates[i] != 1 ) &&
    ( candidates[i] != 8 ) &&
    ( candidates[i] != 6 ) &&
    ( candidates[i] != 15 ) &&
    ( candidates[i] != 48 ) &&
    ( candidates[i] != 57 ) &&
    ( candidates[i] != 55 ) &&
    ( candidates[i] != 62 ) )
{
 return (candidates[i]);
}
}

if ( ( candidates[r] == 1 ) && (cells[0]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 8 ) && (cells[0]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 6 ) && (cells[7]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 15 ) && (cells[7]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 48 ) && (cells[56]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 57 ) && (cells[56]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 55 ) && (cells[63]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 62 ) && (cells[63]==0) )
{
return (candidates[r]);
}                   


r = rand(cnt);

if ( ( candidates[r] != 9 ) && 
  ( candidates[r] != 14 ) && 
  ( candidates[r] != 49 ) &&
  ( candidates[r] != 54 ) &&
  ( candidates[r] != 1 ) &&
  ( candidates[r] != 8 ) &&
  ( candidates[r] != 6 ) &&
  ( candidates[r] != 15 ) &&
  ( candidates[r] != 48 ) &&
  ( candidates[r] != 57 ) &&
  ( candidates[r] != 55 ) &&
  ( candidates[r] != 62 ) )
{
return (candidates[r]);
}

if ( ( candidates[r] == 1 ) && (cells[0]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 8 ) && (cells[0]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 6 ) && (cells[7]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 15 ) && (cells[7]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 48 ) && (cells[56]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 57 ) && (cells[56]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 55 ) && (cells[63]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 62 ) && (cells[63]==0) )
{
return (candidates[r]);
}                   

if ( ( candidates[r] == 9 ) && (cells[0]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 14 ) && (cells[7]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 49 ) && (cells[56]==0) )
{
return (candidates[r]);
}                   
if ( ( candidates[r] == 54 ) && (cells[63]==0) )
{
return (candidates[r]);
}                   

return (candidates[r]);
}

function BuildCandidates(phase)
{
var count=0;
var i,x,y;

num_candidates=0;

for (i=0; i<num_cells; i++)
{
x = i % num_columns;
y = Math.floor(i / num_columns);

count = CheckSquare(x,y,phase);

if ( count > 0 )
{
 candidates[num_candidates]=i;
 num_candidates++;
}
}

return num_candidates;
}

function RecomputeVars(x,y,count)
{
var i,j,c1,c2,str;

i = x + (y*num_columns);
cells[i]=phase;

document.getElementById("Pass").disabled = true;
document.getElementById("StatusId").value = "";

if (phase==1)
{
totals[1]+=(count+1);
totals[2]-=count;
phase=2;
}
else if (phase==2)
{
totals[2]+=(count+1);
totals[1]-=count;
phase=1;
}

options[1]=0;
options[2]=0;

for (j=0; j<num_cells; j++)
{
if ( cells[j] != 0 )
{
 continue;
}
x = j % num_columns;
y = Math.floor(j / num_columns);

c1 = CheckSquare(x,y,1);
if ( c1 > 0 ) options[1]++;
c2 = CheckSquare(x,y,2);
if ( c2 > 0 ) options[2]++;
}
totals[0]--;
if ( (options[1]==0) && (options[2]==0))
{
document.getElementById("Pass").disabled = true;
document.getElementById("Undo").disabled = true;
if ( totals[1] == totals[2] )
{ 
 document.getElementById("StatusId").style.color = '#000066';
 str = "                Draw!!  Let's try another one!!";
}
else if ( totals[1] > totals[2] )
{ 
 document.getElementById("StatusId").style.color = '#008800';
 str = "                Congratulations!!  You won!!";
}
else if ( totals[2] > totals[1] )
{ 
 document.getElementById("StatusId").style.color = '#880000';
 str = "                Sorry!!  The computer won!!";
}
document.getElementById("StatusId").value = str;
game_ended=1;
}
else if ( (options[1]==0) && (phase==1))
{
document.getElementById("Pass").disabled = false;
document.getElementById("StatusId").style.color = '#ff0000';
document.getElementById("StatusId").value = "You have no legal moves, Click on the 'Pass' button!";
}
else if ( (options[2]==0) && (phase==2))
{
PassTurn();
document.getElementById("StatusId").style.color = '#ff0000';
document.getElementById("StatusId").value = "Computer has no legal moves! It is your turn again";
}

if ( game_ended == 1 )
{
document.getElementById("CurrPlay").innerHTML = "";
document.getElementById("TurnId").innerHTML = "";
document.getElementById("a1").className = "ClassTotal1";
document.getElementById("a2").className = "ClassTotal2";
}
}

function UndoMove()
{
if ( game_ended== 1 ) return;
RestoreCells();
Redraw();
RedrawVars();
}

function HandleMouseOver(x,y)
{
if ( game_ended== 1 ) return;
if ( state == 1 ) return;

var count=0;

capture_len=0;
count = CheckSquare(x,y,phase);
if ( count > 0 )
{
var nx,ny,i,ind;
var n = 'td' + '-' + x + '-' + y;
document.getElementById(n).className="cellover"+phase;

for (i=0;i<capture_len;i++)
{
 ind=capture[i];
 nx = ind % num_columns;
 ny = Math.floor(ind / num_columns);
 n = 'td' + '-' + nx + '-' + ny;
 document.getElementById(n).className="cellover"+phase;
}
}
}

function CheckSquare(x,y,val)
{
var i,count=0,total_count=0;
var ur=0,dr=0,ul=0,dl=0;      
var u=0,d=0,l=0,r=0;      

i = x + (y*num_columns);

if ( cells[i]==0 )
{

count = CheckUpRight(x,y,val,1);
if ( count > 0 )
{
 total_count += count;
 ur=count;
}

count = CheckDownRight(x,y,val,1);
if ( count > 0 )
{
 total_count += count;
 dr=count;
}

count = CheckUpLeft(x,y,val,1);
if ( count > 0 )
{
 total_count += count;
 ul=count;
}

count = CheckDownLeft(x,y,val,1);
if ( count > 0 )
{
 total_count += count;
 dl=count;
}

count = CheckUp(x,y,val,1);
if ( count > 0 )
{
 total_count += count;
 u=count;
}

count = CheckDown(x,y,val,1);
if ( count > 0 )
{
 total_count += count;
 d=count;
}

count = CheckLeft(x,y,val,1);
if ( count > 0 )
{
 total_count += count;
 l=count;
}

count = CheckRight(x,y,val,1);
if ( count > 0 )
{
 total_count += count;
 r=count;
}
}

return total_count;
}

function InvertSquare(x,y,val)
{
var i,count=0,total_count=0;
i = x + (y*num_columns);

if ( cells[i]==0 )
{
count = CheckUpRight(x,y,val,0);
if ( count > 0 )
{
 total_count += count;
 InvertUpRight(x,y,val);
}

count = CheckDownRight(x,y,val,0);
if ( count > 0 )
{
 total_count += count;
 InvertDownRight(x,y,val);
}

count = CheckUpLeft(x,y,val,0);
if ( count > 0 )
{
 total_count += count;
 InvertUpLeft(x,y,val);
}

count = CheckDownLeft(x,y,val,0);
if ( count > 0 )
{
 total_count += count;
 InvertDownLeft(x,y,val);
}

count = CheckUp(x,y,val,0);
if ( count > 0 )
{
 total_count += count;
 InvertUp(x,y,val);
}

count = CheckDown(x,y,val,0);
if ( count > 0 )
{
 total_count += count;
 InvertDown(x,y,val);
}

count = CheckLeft(x,y,val,0);
if ( count > 0 )
{
 total_count += count;
 InvertLeft(x,y,val);
}

count = CheckRight(x,y,val,0);
if ( count > 0 )
{
 total_count += count;
 InvertRight(x,y,val);
}
}

return total_count;
}

function CheckUpRight(x,y,val,hlight)
{
var i,iy,ix,count=0,endval=0;

if ( ( y > 0 ) && ( x < num_columns-1 ) )
{
for (iy=y-1,ix=x+1; iy>=0 && ix<num_columns; iy--,ix++)
{
 i = ix + (iy*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   endval=val;
   break;
 }
 else
 {
   count++;
   if (hlight==1)
   {
     capture[capture_len]=i;
     capture_len++;
   }
 }
}
}

if ( endval== 0 )
{
if (hlight==1)
{
 capture_len -=count;
}
count=0; 
}

return count;
}

function InvertUpRight(x,y,val)
{
var i,ix,iy;

if ( ( y > 0 ) && ( x < num_columns-1 ) )
{
for (iy=y-1,ix=x+1; iy>=0 && ix<num_columns; iy--,ix++)
{
 i = ix + (iy*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   break;
 }
 else
 {
   cells[i]=val;
 }
}
}
}

function CheckDownRight(x,y,val,hlight)
{
var i,iy,ix,count=0,endval=0;

if ( ( y <num_lines-1 ) && ( x < num_columns-1 ) )
{
for (iy=y+1,ix=x+1; iy<num_lines && ix<num_columns; iy++,ix++)
{
 i = ix + (iy*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   endval=val;
   break;
 }
 else
 {
   if (hlight==1)
   {
     capture[capture_len]=i;
     capture_len++;
   }
   count++;
 }
}
}

if ( endval== 0 )
{
if (hlight==1)
{
 capture_len -=count;
}
count=0;
}

return count;
}

function InvertDownRight(x,y,val)
{
var i,ix,iy;

if ( ( y < num_lines-1 ) && ( x < num_columns-1 ) )
{
for (iy=y+1,ix=x+1; iy<num_lines && ix<num_columns; iy++,ix++)
{
 i = ix + (iy*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   break;
 }
 else
 {
   cells[i]=val;
 }
}
}
}


function CheckUpLeft(x,y,val,hlight)
{
var i,iy,ix,count=0,endval=0;

if ( ( y > 0 ) && ( x > 0 ) )
{
for (iy=y-1,ix=x-1; iy>=0 && ix>=0; iy--,ix--)
{
 i = ix + (iy*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   endval=val;
   break;
 }
 else
 {
   if (hlight==1)
   {
     capture[capture_len]=i;
     capture_len++;
   }
   count++;
 }
}
}

if ( endval== 0 )
{
if (hlight==1)
{
 capture_len -=count;
}
count=0;
}

return count;
}

function InvertUpLeft(x,y,val)
{
var i,ix,iy;

if ( ( y > 0 ) && ( x > 0 ) )
{
for (iy=y-1,ix=x-1; iy>=0 && ix>=0; iy--,ix--)
{
 i = ix + (iy*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   break;
 }
 else
 {
   cells[i]=val;
 }
}
}
}

function CheckDownLeft(x,y,val,hlight)
{
var i,iy,ix,count=0,endval=0;

if ( ( y <num_lines-1 ) && ( x >0 ) )
{
for (iy=y+1,ix=x-1; iy<num_lines && ix>=0; iy++,ix--)
{
 i = ix + (iy*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   endval=val;
   break;
 }
 else
 {
   if (hlight==1)
   {
     capture[capture_len]=i;
     capture_len++;
   }
   count++;
 }
}
}

if ( endval== 0 )
{
if (hlight==1)
{
 capture_len -=count;
}
count=0;
}

return count;
}

function InvertDownLeft(x,y,val)
{
var i,ix,iy;

if ( ( y < num_lines-1 ) && ( x > 0 ) )
{
for (iy=y+1,ix=x-1; iy<num_lines && ix>=0; iy++,ix--)
{
 i = ix + (iy*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   break;
 }
 else
 {
   cells[i]=val;
 }
}
}
}


function CheckUp(x,y,val,hlight)
{
var i,iy,count=0,endval=0;

if ( y > 0 )
{
for (iy=y-1; iy>=0; iy--)
{
 i = x + (iy*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   endval=val;
   break;
 }
 else
 {
   if (hlight==1)
   {
     capture[capture_len]=i;
     capture_len++;
   }
   count++;
 }
}
}

if ( endval== 0 )
{
if (hlight==1)
{
 capture_len -=count;
}
count=0;
}

return count;
}

function InvertUp(x,y,val)
{
var i,iy;

if ( y > 0 )
{
for (iy=y-1; iy>=0; iy--)
{
 i = x + (iy*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   break;
 }
 else
 {
   cells[i]=val;
 }
}
}
}

function CheckDown(x,y,val,hlight)
{
var i,iy,count=0,endval=0;

for (iy=y+1; iy<num_lines; iy++)
{
i = x + (iy*num_columns);
if ( cells[i]==0 )
{
 break;
}
else if ( cells[i]==val )
{
 endval=val;
 break;
}
else
{
 if (hlight==1)
 {
   capture[capture_len]=i;
   capture_len++;
 }
 count++;
}
}

if ( endval== 0 )
{
if (hlight==1)
{
 capture_len -=count;
}
count=0;
}

return count;
}


function InvertDown(x,y,val)
{
var i,iy;

if ( y < num_lines-1 )
{
for (iy=y+1; iy<num_lines; iy++)
{
 i = x + (iy*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   break;
 }
 else
 {
   cells[i]=val;
 }
}
}
}

function CheckLeft(x,y,val,hlight)
{
var i,ix,count=0,endval=0;

if ( x > 0 )
{
for (ix=x-1; ix>=0; ix--)
{
 i = ix + (y*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   endval=val;
   break;
 }
 else
 {
   if (hlight==1)
   {
     capture[capture_len]=i;
     capture_len++;
   }
   count++;
 }
}
}

if ( endval== 0 )
{
if (hlight==1)
{
 capture_len -=count;
}
count=0;
}

return count;
}

function InvertLeft(x,y,val)
{
var i,ix;

if ( x > 0 )
{
for (ix=x-1; ix>=0; ix--)
{
 i = ix + (y*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   break;
 }
 else
 {
   cells[i]=val;
 }
}
}
}

function CheckRight(x,y,val,hlight)
{
var i,ix,count=0,endval=0;

if ( x < num_columns-1 )
{
for (ix=x+1; ix<num_columns; ix++)
{
 i = ix + (y*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   endval=val;
   break;
 }
 else
 {
   if (hlight==1)
   {
     capture[capture_len]=i;
     capture_len++;
   }
   count++;
 }
}
}

if ( endval== 0 )
{
if (hlight==1)
{
 capture_len -=count;
}
count=0;
}

return count;
}

function InvertRight(x,y,val)
{
var i,ix;

if ( x < num_columns-1 )
{
for (ix=x+1; ix<num_columns; ix++)
{
 i = ix + (y*num_columns);
 if ( cells[i]==0 )
 {
   break;
 }
 else if ( cells[i]==val )
 {
   break;
 }
 else
 {
   cells[i]=val;
 }
}
}
}

function HandleMouseOut(x,y)
{
if ( game_ended== 1 ) return;
if ( state == 1 ) return;

UnSelect(x,y);
}

function UnSelect(x,y)
{
var nx,ny,i,ind;
var n = 'td' + '-' + x + '-' + y;

document.getElementById(n).className="cellout";

for (i=0;i<capture_len;i++)   
{
ind=capture[i];
nx = ind % num_columns;
ny = Math.floor(ind / num_columns);
n = 'td' + '-' + nx + '-' + ny;
document.getElementById(n).className="cellout";
}
capture_len=0;
}

-->
</script>