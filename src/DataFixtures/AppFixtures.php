<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création des produits
        $productsData = [
            [
                'name' => "Kit d'hygiène recyclabe",
                'shortDescription' => "Pour une salle de bain éco-friendly",
                'fullDescription' => "Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante.

                Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée.

                Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.",
                'price' => 24.99,
                'picture' => '/img/products/kit-hygiene.png'
            ],
            [
                'name' => "Shot tropical",
                'shortDescription' => "Fruits frais, pressés à froid",
                'fullDescription' => "Découvrez le Shot Tropical, une explosion de fraîcheur et de saveurs exotiques dans chaque gorgée.

                Préparé à partir de fruits frais, soigneusement sélectionnés pour leur qualité, ce shot est pressé à froid pour conserver toutes les vitamines et nutriments essentiels. Idéal pour un coup de boost matinal ou une pause rafraîchissante tout au long de la journée.

                Chaque bouteille de 250 ml vous offre une boisson naturelle et revitalisante, sans sucre ajouté ni conservateurs, pour une expérience authentique et pure.",
                'price' => 4.50,
                'picture' => '/img/products/shot-tropical.png' 
            ],
            [
                'name' => "Gourde en bois",
                'shortDescription' => "50cl, bois d'olivier",
                'fullDescription' => "La Gourde en Bois d'Olivier est la solution élégante et écologique pour rester hydraté tout au long de la journée.

                Fabriquée à partir de bois d'olivier durable et soigneusement poli, cette gourde de 50 cl allie robustesse et esthétique. Sa conception en bois assure une isolation thermique naturelle, maintenant vos boissons à la température idéale.

                En plus de son design raffiné, cette gourde est une alternative respectueuse de l'environnement aux contenants plastiques traditionnels, vous permettant de réduire votre empreinte écologique avec style.",
                'price' => 16.90,
                'picture' => '/img/products/gourde-bois.png'
            ],
            [
                'name' => "Disques Démaquillants x3",
                'shortDescription' => "Solution efficace pour vous démaquiller en douceur",
                'fullDescription' => "Les Disques Démaquillants x3 sont la solution parfaite pour un démaquillage doux et efficace.

                Fabriqués à partir de matériaux doux et absorbants, ces disques permettent d'éliminer le maquillage et les impuretés en un seul geste, tout en respectant la sensibilité de votre peau. Leur conception durable et réutilisable en fait une alternative écologique aux produits jetables.

                Chaque pack contient trois disques, idéaux pour un démaquillage quotidien, tout en réduisant votre impact environnemental.",
                'price' => 19.90,
                'picture' => '/img/products/disques.png'
            ],
            [
                'name' => "Bougie Lavande & Patchouli",
                'shortDescription' => "Cire naturelle",
                'fullDescription' => "La Bougie Lavande & Patchouli est conçue pour offrir une expérience sensorielle unique avec des arômes apaisants et équilibrants.

                Fabriquée à partir de cire naturelle, cette bougie diffuse une agréable fragrance de lavande et de patchouli, créant une atmosphère relaxante dans n'importe quel espace. La cire naturelle assure une combustion propre et uniforme, tout en réduisant l'impact environnemental.

                Parfaite pour créer une ambiance chaleureuse et réconfortante, cette bougie est également un choix éco-responsable pour embellir votre intérieur.",
                'price' => 32.00,
                'picture' => '/img/products/bougies.png'
            ],
            [
                'name' => "Brosse à dent",
                'shortDescription' => "Bois de hêtre rouge issu de forêts gérées durablement",
                'fullDescription' => "La Brosse à Dent en Bois de Hêtre Rouge est un choix élégant et écologique pour vos soins bucco-dentaires.

                Conçue à partir de bois de hêtre rouge, cette brosse est issue de forêts gérées durablement, garantissant une gestion responsable des ressources naturelles. Ses poils en nylon sont doux et efficaces pour un nettoyage en profondeur sans compromettre le confort.

                Avec son design ergonomique et son engagement en faveur de l'environnement, cette brosse à dents est une alternative sophistiquée aux produits en plastique.",
                'price' => 5.40,
                'picture' => '/img/products/brosse-a-dent.png'
            ],
            [
                'name' => "Kit couvert en bois",
                'shortDescription' => "Revêtement Bio en olivier & sac de transport",
                'fullDescription' => "Le Kit Couvert en Bois est l'option parfaite pour un repas en plein air ou pour vos repas quotidiens avec une touche éco-responsable.

                Chaque kit comprend des couverts en bois d'olivier, connus pour leur durabilité et leur beauté naturelle, ainsi qu'un sac de transport pratique et élégant. Le revêtement bio en olivier offre une résistance accrue et un design raffiné.

                Ce kit est idéal pour ceux qui cherchent à réduire leur impact environnemental tout en profitant d'un produit de qualité supérieure.",
                'price' => 12.30,
                'picture' => '/img/products/kit-couverts.png'
            ],
            [
                'name' => "Nécessaire, déodorant Bio",
                'shortDescription' => "50ml déodorant à l’eucalyptus",
                'fullDescription' => "Le Déodorant Bio à l’Eucalyptus est une option naturelle et efficace pour rester frais toute la journée.

                Enrichi en eucalyptus, ce déodorant de 50 ml est formulé avec des ingrédients biologiques pour offrir une protection durable sans compromettre la santé de votre peau ni l'environnement. Son action antibactérienne naturelle lutte contre les mauvaises odeurs tout en respectant le pH de votre peau.

                Idéal pour ceux qui recherchent une alternative saine aux déodorants conventionnels, ce produit offre une sensation de fraîcheur et de confort tout au long de la journée.",
                'price' => 8.50,
                'picture' => '/img/products/necessaire-deo.png'
            ],
            [
                'name' => "Savon Bio",
                'shortDescription' => "Thé, Orange & Girofle",
                'fullDescription' => "Le Savon Bio Thé, Orange & Girofle est une expérience de lavage luxueuse qui allie des ingrédients naturels pour une peau douce et rafraîchie.

                Ce savon est formulé avec des extraits de thé, d'orange et de girofle, créant une combinaison harmonieuse d'arômes et de bienfaits pour la peau. L'extrait de thé offre des propriétés antioxydantes, tandis que l'orange et le girofle apportent une touche de vivacité et de chaleur.

                Chaque barre de savon est fabriquée à partir d'ingrédients biologiques pour assurer une douceur maximale tout en respectant l'environnement.",
                'price' => 18.90,
                'picture' => '/img/products/savon-bio.png'
            ],
        ];


        foreach ($productsData as $productData) {
            $product = new Product();
            $product->setName($productData['name'])
                ->setShortDescription($productData['shortDescription'])
                ->setFullDescription($productData['fullDescription'])
                ->setPrice($productData['price'])
                ->setPicture($productData['picture']);

            $manager->persist($product);
        }

        // Création d'un utilisateur
        $usersData = [
            [
                'first_name' => 'John',
                'last_name' => 'Sheppard',
                'email' => 'john@me.com',
                'password' => 'john',
                'roles' => ['ROLE_USER'],
                'isApiAccessEnabled' => false
            ],
        ];

        foreach ($usersData as $userData) {
            $user = new User();
            $user->setFirstName($userData['first_name']);
            $user->setLastName($userData['last_name']);
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setApiAccessEnabled($userData['isApiAccessEnabled']);

            // Hachage du mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        $manager->flush();

    }
}
