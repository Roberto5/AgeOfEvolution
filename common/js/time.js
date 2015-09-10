var res=new Array(0,0,0);
var count=new Array();
var init=0;
var initc=0;
var $counter;
var rel=false;
var reload=true;
function IsNumeric(val) {
    if (parseFloat(val)!=val) {
          return false;
     }
     return true;
}
function resetinit()
{
    initc=0;
}
function reload() {
	rel=true;
}
function resetres() {
	init=0;
}
function counter()
{
    // aggiornamento risorse
    $resource=$("#resbar"+ev.focus.id+" .resource");
    for (i=0;i<$resource.length;i++)
    {
        now=$resource.eq(i).text();
        storage=parseInt(now.substr(now.indexOf("/")+1));
        now=parseInt(now);
        if(now>=storage) new_val=storage;
        else {
            prod=$resource.eq(i).attr("title")/3600;
            if (init<3) {
                res[i]=now;
                init++;
            }
            res[i]=res[i]*1+prod*1;
            new_val=parseInt(res[i]);
        }
        $resource.eq(i).text(new_val+"/"+storage);
    }
    n=count.length;
    //if (n==0) n=1;
    d=new Date();
    
    if (initc<1) {
    	temp=new Array();
    	$counter=$(".countDown");
    	n=$counter.length;
    	for (i=0;i<n;i++)
    	{
        	c=$counter.eq(i).text();
        	if (IsNumeric(c)) temp[i]=c; else temp[i]=-1;
    	}
    	if (!in_array(-1,temp)) reload=true;
    	count=temp;
    }
    
    for (var i in count)
    {
        if (count[i]>-1) count[i]--;
        else {
        	if (reload) {
        		count[i]=0;
        		reload=false;
        	}
        	
        }
        //aggiungere tutorial
        if (count[i]<0) $counter.eq(i).text("00:00:0?");
        else
        $counter.eq(i).text(timeStampToString(count[i]));
    }
    initc=1;

    ts=$("#timestamp").text();
    ts=ts*1+1000;
    $("#timestamp").text(ts);
    d=new Date();
    d.setTime(ts);
    if (d.getMinutes()<10) m="0"+d.getMinutes(); else m=d.getMinutes();
    if (d.getSeconds()<10) s="0"+d.getSeconds(); else s=d.getSeconds();
    if (d.getHours()<10) h="0"+d.getHours(); else h=d.getHours();
    y=d.getFullYear();
    if (d.getMonth()<9) M="0"+(parseInt(d.getMonth())+1); else M=parseInt(d.getHours())+1;
    if (d.getDate()<10) d="0"+d.getDate(); else d=d.getDate();
    $("#timedisp").text(d+"/"+M+"/"+y+" "+h+":"+m+":"+s);
    
    time=$(".time").get();
    for (i=0;time[i];i++) {
        t=$(".time").eq(i).text();
        id=$(".time").eq(i).attr('id');
        d=new Date();
        t=t*1+ts*1;
        d.setTime(t);
        if (d.getMinutes()<10) m="0"+d.getMinutes(); else m=d.getMinutes();
        if (d.getSeconds()<10) s="0"+d.getSeconds(); else s=d.getSeconds();
        $("#"+id+"disp").text(d.getHours()+":"+m+":"+s);
    }
    
    if (in_array(0,count)&&!ev.flagLoader&&(module!='default')) {
    	if (rel) {location.reload();rel=false;ev.flagLoader=true;}
    	else
    	ev.request(module+"/index/refresh", "post", {ajax:"true"});
    } 
    
    window.setTimeout(counter,1000);
}
