<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
   <meta charset="utf-8">
   <title>Import PDF</title>
   <link rel="stylesheet" href="public/css/bootstrap.min.css">
   <script src="public/javascript/jquery-3.4.1"></script>
   <script src="public/javascript/bootstrap.min.js"></script>
   <script>
      // ajaxStart et ajaxStop permettent de désactiver le bouton submit et d'afficher l'image de chargement tant que le serveur n'a pas renvoyé de réponses, puis de réactiver et de cacher
      $(document).ajaxStart(function() {
         $('#loading').show();
         $("#succes").hide();
         $("#submit").attr("disabled", true);
      });
      $(document).ajaxStop(function() {
         $('#loading').hide();
         $("#submit").attr("disabled", false);
      });
      $(document).ready(function() {
         $('#loading').hide();
         $("#succes").hide();
         $("#PDFUploadFile").change(function() {
            var fichier = $('#PDFUploadFile').prop('files')[0];
            if (fichier != undefined) {
               var form_data = new FormData();
               form_data.append('file', fichier);
               // code Ajax envoyant le fichier au PHP, et affichant le retour du serveur
               $.ajax({
                  type: 'POST',
                  url: 'Traitement_Donnees_Compte_Pages.php',
                  contentType: false,
                  processData: false,
                  data: form_data,
                  success: function(reponse) {
                     if (reponse == 'failure' || reponse == 'notPDF' || reponse == 'tooHeavy') {
                        $("#succes").hide();
                        //Affichage des différents messages d'erreurs
                        if (reponse == 'failure') {
                           alert('Le traitement du fichier à échoué');
                        } else if (reponse == 'notPDF') {
                           alert('Le fichier n\'est pas un PDF.');
                        } else if (reponse == 'tooHeavy') {
                           alert('Le fichier envoyé est trop lourd.');
                        }
                        //Si erreur -> on remet les valeurs des inputs à 0, et on remet à 0 l'upload
                        $("#PDFUploadFile").replaceWith($("#PDFUploadFile").val('').clone(true));
                        $("#nbPages").attr("value", "0").attr("placeholder", "0");
                        $("#nbPagesC").attr("value", "0").attr("placeholder", "0");
                        $("#nbPagesNB").attr("value", "0").attr("placeholder", "0");
                     } else {
                        var obj = JSON.parse(reponse);
                        // Retour et affichage de la phrase récapitulative, et peuplement des attributs "value" et "placeholder" des inputs avec les valeurs de retour
                        var paragInfo = "";
                        if (obj.NbPagesNB == obj.NbPages) {
                           paragInfo = "Votre document comporte " + obj.NbPages + " pages, toutes en noir et blanc.";
                        } else if (obj.NbPagesC == obj.NbPages) {
                           paragInfo = "Votre document comporte " + obj.NbPages + " pages, toutes en couleur.";
                        } else {
                           paragInfo = "Votre document comporte " + obj.NbPages + " pages, dont " + obj.NbPagesC + " en couleurs et " + obj.NbPagesNB + " en noir & blanc.";
                        }
                        $("#succes").show().text(paragInfo);
                        $("#nbPages").attr("value", obj.NbPages).attr("placeholder", obj.NbPages);
                        $("#nbPagesC").attr("value", obj.NbPagesC).attr("placeholder", obj.NbPagesC);
                        $("#nbPagesNB").attr("value", obj.NbPagesNB).attr("placeholder", obj.NbPagesNB);
                     }
                  }
               });
               $("#erreur").hide();
            }
         })
      })
   </script>
</head>

<!-- Si vous integrez le programme dans une nouvelle page, attention à :
         -Bien conserver les id des input (myForm, nbPagesNB, nbPagesC, submit, PDFUploadFile, succes)
         -Ne pas oublier les dépendances externes (jQuery, et l'image de chargement se trouvant dans /public/images)
         -Copier le code jQuery tel quel
-->

<body>
   <form method='post' action='' name='myform' id="myForm" enctype='multipart/form-data'>
      <div class="form-group">
         <label for="nbPages">Nombre de pages : </label>
         <input type="text" class="form-control" id="nbPages" name="nbPages" placeholder="0" value="" disabled>
      </div>
      <div class="form-group">
         <label for="nbPagesNB">Nombre de pages Noir & Blanc : </label>
         <input type="text" class="form-control" id="nbPagesNB" placeholder="0" value="" disabled>
      </div>
      <div class="form-group">
         <label for="nbPagesC">Nombre de pages Couleurs : </label>
         <input type="text" class="form-control" id="nbPagesC" placeholder="0" value="" disabled>
      </div>
      <div class="file-path-wrapper">
         <input type="file" class="file-path validate" id="PDFUploadFile" name="PDFUploadFile">
      </div>
      <button type="submit" id="submit" class="btn btn-primary">Envoyer</button>
   </form>
   <img src="public/images/spinner.gif" id="loading">
   <p id="succes"></p>
</body>

</html>
