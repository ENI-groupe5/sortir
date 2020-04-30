/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';


// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';
let path = window.location.pathname;
if(path.includes('sortie/creer')){
    document.getElementById('sortie_lieu').addEventListener("change",afficherLieu);
}
function afficherLieu(){
    let lieu = document.getElementById('sortie_lieu');
    let value = lieu.value;
    let json = require('../../public/results.json');
    let ville = document.getElementById('ville');
    let rue = document.getElementById('rue');
    let cp = document.getElementById('cp');
    let latitude =document.getElementById('lat');
    let longitude = document.getElementById('long');

    for (let $i=0 ; $i < json.posts.length; $i++){
        if ((json["posts"][$i].id) == value){
            console.log(json["posts"][$i]);
            document.getElementById('labelville').innerText = 'Ville : '
            ville.innerText = json["posts"][$i].nomVille;
            document.getElementById('labelrue').innerText = 'Rue : '
            rue.innerText = json["posts"][$i].rue;
            document.getElementById('labelcp').innerText = 'Code Postal : '
            cp.innerText = json["posts"][$i].cp;
            document.getElementById('labellat').innerText = 'Latitude : '
            latitude.innerText = json["posts"][$i].latitude;
            document.getElementById('labellong').innerText = 'Longitude : '
            longitude.innerText = json["posts"][$i].longitude;

            break;
        }
    }




}

