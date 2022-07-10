import comportstar from './script-comportstar.js';

let star=document.querySelectorAll(".starform");

function start(){
  for (let i=0;i<star.length;i++) {
    star[i].addEventListener("mouseover",function(){comportstar.hoverstar(i+1)});
    star[i].addEventListener("mouseout",function(){comportstar.nothoverstar()});
    star[i].addEventListener("click",function(){comportstar.clickstar(i+1)});
  }
}

export default {
  start,
}
