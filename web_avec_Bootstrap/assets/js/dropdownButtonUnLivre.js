window.addEventListener("load",function(){dropDownActualiser();});
window.addEventListener("resize",function(){dropDownActualiser();});

function dropDownActualiser(){
    if($(window).width()>768){
      $("#dropdownMenuUnLivre").css("left",-($("#dropdownMenuUnLivre").width()-$("#dropdownButtonUnLivre").width()));
    } else {
      $("#dropdownMenuUnLivre").css("left",0);
    }
  }
