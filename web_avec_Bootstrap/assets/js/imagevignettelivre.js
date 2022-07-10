window.addEventListener("load",function(){start();});
window.addEventListener("resize",function(){start();});

function start(){
  for(let i=0;i<$(".imgvignettelivre").length;i++){
    let img=$(".imgvignettelivre")[i];
    let parent=$(".imgvignettelivre").parent().parent()[i];
    let width=0;
    let height=0;

    let widthBase=img.width;
    let heightBase=img.height;

    height=parseInt($(img).css("max-height"));
    console.log(height);
    width=(height*widthBase)/heightBase;

    if(width>parent.width){
      width=parent.width;
      height=(width*heightBase)/widthBase;
    }
    img.width=width;
    img.height=height;
  }
}

export default{
  start,
}
