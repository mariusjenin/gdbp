import imagevignettelivre from './imagevignettelivre.js';

$(document).ready(function () {
    $('.form-control').keyup(function () {
        let texte = $(this).val();
        $.ajax({
            type: 'GET',
            url: document.URL + '/rechercheAjax',
            data: 'texte=' + texte,
            dataType: "json",
            success: function (data) {
                $('#adaptive-content').html('');
                let html = "";
                let comp = 0;
                if (data.length!==0) {
                    data.forEach((e) => {
                        html += `<div class="col-lg-3 col-md-6 mb-4" style="padding-bottom:10px">
						<div class="text-center card h-100" style="min-height:500px;padding-bottom:10px;border:solid 1px #ccc;border-radius:5px">
							<a href="${e['urlLivre']}"><img class="imgvignettelivre card-img-top" src="${e['urlImage']}" style="max-height: 400px" alt="image livre"></a>
							<div class="card-body">
								<h4 class="card-title">
									<a href="${e['urlLivre']}">${e['titre']}</a>
								</h4>
								<p class="card-text" style="padding:5px">${e['description']}</p>
							</div>
							<div class="card-footer">
								<p>${e['score']}</p>
							</div>
						</div>
					</div>`;
                        comp++;
                        if (comp % 2 === 0 && comp!=0) {
                            html += `<!-- Séparateur toutes les 2 cartes -->
					<div class=\"col-xs-12 d-none d-sm-none d-lg-none d-md-block\"></div>
					<!-- -->`;
        } else if (comp % 4 === 0 && comp!=0) {
                            html += `<!-- Séparateur toutes les 4 cartes -->
					<div class=\"col-xs-12 d-none d-sm-none d-md-none d-lg-block\"></div>
					<!-- -->`;
                        }
                    });
                } else {
                    html="Aucun livre ne correspond à ce nom !"
                }
                $('#adaptive-content').append(html);
                imagevignettelivre.start();
            },
            error: function () {
                $('#adaptive-content').html('');
                $('#adaptive-content').append("Unable to load your data");
            }
        });
    });
});
