document.addEventListener('DOMContentLoaded', () => {

    // ===== 1. GESTION DES LIKES EN AJAX =====
    const boutonsLike = document.querySelectorAll('.btn-like');

    boutonsLike.forEach(bouton => {
        bouton.addEventListener('click', async () => {
            const idPublication = bouton.dataset.id;
            const compteurElement = document.getElementById(`likes-${idPublication}`);

            try {
                const reponse = await fetch(`traiter_like.php?id=${idPublication}`);
                
                if (!reponse.ok) throw new Error("Erreur serveur");
                
                const nouveauNombre = await reponse.text();
                
                // Mise à jour affichage
                compteurElement.textContent = nouveauNombre;
                bouton.classList.toggle('aime');

            } catch (erreur) {
                console.error("Erreur lors du like :", erreur);
                alert("Impossible de mettre à jour le like, réessayez plus tard.");
            }
        });
    });


    // ===== 2. SCROLL AUTOMATIQUE MESSAGERIE =====
    const zoneMessages = document.querySelector('.zone-messages');
    if (zoneMessages) {
        zoneMessages.scrollTop = zoneMessages.scrollHeight;
    }


    // ===== 3. CONFIRMATIONS ACTIONS SENSIBLES =====
    const liensSuppression = document.querySelectorAll('.btn.rouge');
    liensSuppression.forEach(lien => {
        lien.addEventListener('click', (e) => {
            const message = lien.textContent.includes('Retirer') 
                ? "Voulez-vous vraiment retirer cet ami ?" 
                : "Voulez-vous vraiment supprimer cet élément ?";

            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });


    // ===== 4. PREVISUALISATION IMAGE PUBLICATION =====
    const champImagePub = document.querySelector('input[name="image"]');
    if (champImagePub) {
        champImagePub.addEventListener('change', (e) => {
            const fichier = e.target.files[0];
            if (fichier && fichier.size > 5 * 1024 * 1024) {
                alert("L'image ne doit pas dépasser 5 Mo !");
                champImagePub.value = '';
            }
        });
    }


    // ===== 5. PREVISUALISATION PHOTO PROFIL =====
    const champPhotoProfil = document.querySelector('input[name="photo"]');
    const photoProfil = document.querySelector('.photo-profil');

    if (champPhotoProfil && photoProfil) {
        champPhotoProfil.addEventListener('change', (e) => {
            const fichier = e.target.files[0];
            if (fichier) {
                if (fichier.size > 2 * 1024 * 1024) {
                    alert("La photo ne doit pas dépasser 2 Mo !");
                    champPhotoProfil.value = '';
                    return;
                }

                const lecteur = new FileReader();
                lecteur.onload = (evt) => {
                    photoProfil.src = evt.target.result;
                };
                lecteur.readAsDataURL(fichier);
            }
        });
    }

});

