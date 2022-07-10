let etatstar=[true,true,true,true,true];
let star=document.querySelectorAll(".starform");

function getnbstar(){
    let nb=0;
    for (let i=0;i<star.length;i++) {
      if(etatstar[i]){
        nb++;
      }
    }
    return nb;
}

function hoverstar(num){
  for (let i=0;i<num;i++) {
    star[i].classList.add("fas");
    star[i].classList.remove("far");
  }
  for (let i=num;i<star.length;i++) {
    star[i].classList.remove("fas");
    star[i].classList.add("far");
  }
}

function nothoverstar(){
  for (let i=0;i<etatstar.length;i++) {
    if(etatstar[i]){
      star[i].classList.add("fas");
      star[i].classList.remove("far");
    }else{
      star[i].classList.remove("fas");
      star[i].classList.add("far");
    }
  }
}

function clickstar(num){
  $("#starsPost").attr("value",num);
  if(num==1 && etatstar[0] && !etatstar[1]){
    etatstar[0]=false;
    star[0].classList.remove("fas");
    star[0].classList.add("far");
    $("#starsPost").attr("value",0);
  } else {
    for (let i=0;i<num;i++) {
      etatstar[i]=true;
    }
    for (let i=num;i<star.length;i++) {
      etatstar[i]=false;
    }
  }
}

export default{
  hoverstar,
  nothoverstar,
  clickstar,
  getnbstar,
}
