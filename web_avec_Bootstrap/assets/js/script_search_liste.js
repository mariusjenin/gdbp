$(document).ready(function () {
    $('.form-control').keyup(function () {
        var texte = $(this).val();
        $.ajax({
            type: 'GET',
            url: document.URL + '/rechercheAjax',
            data: 'texte=' + texte,
            dataType: "json",
            success: function (data) {
                $('#adaptive-content').html('');
                comp = 0;
                let html ="";
                if (data.length!==0) {
                    data.forEach((e) => {
                         html += `<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-4" style="margin-bottom:30px">
            <div class="text-center card h-100" style="border:solid 1px #ccc;border-radius:5px">
              <div class="col-sm-12" style="padding:0px;padding-bottom:10px">
                <a href="${e['urlListe']}">
                  <div class="imagegrillelistes imagegrillelistes1 col-xs-6" style="background-image:url(${e['urlImage1']})"></div>
                  <div class="imagegrillelistes imagegrillelistes2 col-xs-6" style="background-image:url(${e['urlImage2']})"></div>
                  <div class="imagegrillelistes imagegrillelistes3 col-xs-6" style="background-image:url(${e['urlImage3']})"></div>
                  <div class="imagegrillelistes imagegrillelistes4 col-xs-6" style="background-image:url(${e['urlImage4']})"></div>
                </a>
              </div>
              <div class="card-body">
                <h4 class="card-title">
                  <a href="${e['urlListe']}">${e['nom']}</a>
                </h4>
                <p class="card-text" style="padding:5px">${e['description']}</p>
              </div>
              <div class="card-footer">
                <p>${e['score']}</p>
              </div>
            </div>
          </div>`;
                        if (comp % 2 === 0) {
                            html += `<!-- Séparateur toutes les 2 cartes -->
					<div class=\"col-xs-12 d-none d-sm-none d-lg-none d-md-block\"></div>
					<!-- -->`;
                        } else if (comp % 3 === 0) {
                            html += ``;
                        } else if (comp % 4 === 0) {
                            html += `<!-- Séparateur toutes les 4 cartes -->
					<div class=\"col-xs-12 d-none d-sm-none d-md-none d-lg-block\"></div>
					<!-- -->`;
                        }
                        comp++;
                    });
                } else {
                    html = "Aucune liste ne correspond à ce nom !";
                }
                $('#adaptive-content').append(html);
            },
            error: function (url) {
                $('#adaptive-content').html('');
                $('#adaptive-content').append("Unable to load your data");
            }
        });
    });
});