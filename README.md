# 🎵 Musae — Application de gestion d’entraînements musicaux
*Projet Laravel + Vue.js / Vite — Documentation officielle*

---

## 📌 Présentation

**Musae** est une application web permettant aux musiciens d’organiser leurs entraînements, gérer leurs partitions, suivre leur progression, et planifier leurs sessions de pratique.  
Elle propose une interface simple, moderne et intuitive, ainsi qu’une API documentée pour faciliter l’évolution du projet.

Musae répond à un besoin réel : **structurer la pratique musicale**, souvent dispersée, non suivie, et difficile à analyser.

---

## 🚀 Fonctionnalités principales

### 🎼 Gestion des partitions
- Importation de partitions (PDF, images)
- Organisation par instrument, difficulté, compositeur
- Consultation via lecteur intégré

### 🎹 Gestion des instruments
- Ajout / modification / suppression
- Association partitions ↔ entraînements

### 🕒 Gestion des entraînements
- Création d’un entraînement avec :
  - date
  - durée
  - instrument
  - partition
  - notes personnelles
- Historique complet
- Visualisation de la progression

### 📊 Analyse & historique
- Statistiques de pratique
- Filtrage par période, instrument, partition

### 💬 Messagerie interne
- Messages entre utilisateurs
- Notifications internes

### 👤 Gestion des utilisateurs
- Authentification Laravel Fortify
- Profils utilisateurs
- Rôles (admin / utilisateur)

---

## 🏗️ Architecture technique

### Front‑end
- Vue.js 3 + Vite  
- Composition API  
- Axios  
- TailwindCSS  
- Vue Router  

### Back‑end
- Laravel 10  
- API REST  
- Eloquent ORM  
- Migrations + Seeders  
- Form Requests  
- Auth Sanctum  
- Documentation Swagger

### Base de données
- MySQL / MariaDB  
- Modèles principaux :
  - User  
  - Instrument  
  - Partition  
  - Entrainement  
  - Message  

---

## 📚 Documentation API (Swagger)

Accessible via :
http://localhost:8000/api/documentation


Endpoints documentés :
- `/api/instruments`
- `/api/partitions`
- `/api/entrainements`
- `/api/messages`
- `/api/users`

---

## 🛠️ Installation & lancement

### 🔧 Prérequis
- PHP ≥ 8.2  
- Composer  
- Node.js ≥ 18  
- MySQL / MariaDB  
- (Optionnel) Docker + Laravel Sail

---

## 📥 Installation du back‑end (Laravel)

```bash
git clone https://github.com/ton-projet/musae.git
cd musae/backend

composer install
cp .env.example .env
php artisan key:generate
```

Base de données
Configurer .env :


```bash
DB_DATABASE=musae
DB_USERNAME=root
DB_PASSWORD=
```
Puis :
```bash
php artisan migrate --seed
```

Lancer le serveur
```bash
php artisan serve
```

---
## 🖥️ Installation du front‑end (Vue.js)
```bash
cd musae/frontend
npm install
npm run dev
```

---
## 📂 Structure du projet
Back‑end
```bash
backend/
 ├── app/
 │   ├── Models/
 │   ├── Http/
 │   │   ├── Controllers/
 │   │   ├── Requests/
 │   └── ...
 ├── database/
 │   ├── migrations/
 │   ├── seeders/
 └── routes/
     └── api.php
```

Front-end
```bash
frontend/
 ├── src/
 │   ├── components/
 │   ├── pages/
 │   ├── services/
 │   ├── router/
 │   └── assets/
```

---
## 🧪 Tests
Back‑end
```bash
php artisan test
```

Tests disponibles :

* API Users

* API Instruments

* API Partitions

* API Entrainements

* API Messages

---
## 🔐 Authentification
Musae utilise Laravel Sanctum pour :
* les tokens API,
* la gestion de session,
* la protection des routes.

Endpoints :
* /api/login
* /api/register
* /api/logout

---
## 🧭 Roadmap
* Calendrier d’entraînements
* Système de badges / gamification
* Export PDF des statistiques
* Mode hors‑ligne
* Partage de partitions

---
## 🤝 Contributeurs
Arthur — Développeur Front-end & back-end
Ines - Développeuse Front-end & back-en

Encadrants / enseignants du projet Musae

---

## 📄 Licence
Projet académique — utilisation libre dans le cadre pédagogique.

---

## 🔗 Ressources externes

<details>
<summary><strong>🌐 Références Vue.js</strong></summary>

- **MadeWithVueJS** — Galerie de projets construits avec Vue.js  
  🔗 https://madewithvuejs.com/

</details>

<details>
<summary><strong>📅 Calendrier utilisé dans Musae</strong></summary>

- **Vue‑Cal** — Composant de calendrier avancé (drag & drop, multi‑vue, événements)  
  🔗 https://github.com/antoniandre/vue-cal

</details>

<details>
<summary><strong>🎨 Framework UI utilisé dans Musae</strong></summary>

- **Wave‑UI** — Framework UI léger et élégant pour Vue.js  
  🔗 https://github.com/antoniandre/wave-ui

</details>
