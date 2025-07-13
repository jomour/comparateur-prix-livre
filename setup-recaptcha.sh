#!/bin/bash

echo "🔒 Configuration reCAPTCHA pour le Comparateur de Prix Manga"
echo "=========================================================="
echo ""

# Vérifier si le fichier .env existe
if [ ! -f .env ]; then
    echo "❌ Fichier .env non trouvé. Veuillez créer un fichier .env basé sur .env.example"
    exit 1
fi

echo "📝 Ajout des variables reCAPTCHA au fichier .env..."
echo ""

# Ajouter les variables reCAPTCHA si elles n'existent pas déjà
if ! grep -q "RECAPTCHA_SITE_KEY" .env; then
    echo "# reCAPTCHA Configuration" >> .env
    echo "RECAPTCHA_SITE_KEY=your_recaptcha_site_key_here" >> .env
    echo "RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key_here" >> .env
    echo "✅ Variables reCAPTCHA ajoutées au fichier .env"
else
    echo "ℹ️  Variables reCAPTCHA déjà présentes dans .env"
fi

echo ""
echo "🔧 Configuration terminée !"
echo ""
echo "📋 Prochaines étapes :"
echo "1. Obtenez vos clés reCAPTCHA sur https://www.google.com/recaptcha/admin"
echo "2. Remplacez les valeurs dans .env :"
echo "   - RECAPTCHA_SITE_KEY=votre_cle_site_ici"
echo "   - RECAPTCHA_SECRET_KEY=votre_cle_secrete_ici"
echo "3. Redémarrez votre serveur Laravel"
echo ""
echo "📖 Consultez RECAPTCHA_SETUP.md pour plus de détails" 