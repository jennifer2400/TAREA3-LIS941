<?php
/**
 * ============================================================================
 * la cuponera sv - controlador página de inicio (home)
 * ============================================================================
 * gestiona la página de inicio pública, presentación de la plataforma y
 * ofertas demostrativas claramente rotuladas para evitar confusión.
 */

class HomeController
{
    public function index(): void
    {
        // La portada promocional es exclusiva de visitantes.
        // Cada usuario autenticado entra a su panel segun el rol.
        if (Auth::check()) {
            Auth::redirectToDashboard();
        }

        // ofertas promocionales de ejemplo, claramente identificadas como demostrativas
        $promocionesDemo = [
            [
                'id'             => 1,
                'titulo'         => 'Pizza Suprema Familiar 2x1 + Bebidas',
                'empresa'        => 'Pizzería La Nostra',
                'rubro'          => 'Gastronomía',
                'precio_regular' => 25.00,
                'precio_oferta'  => 12.50,
                'descuento'      => 50,
                'fecha_limite'   => date('Y-m-d', strtotime('+15 days')),
                'descripcion'    => 'Dos pizzas grandes de especialidad con masa artesanal más dos refrescos naturales de 1 litro.',
                'badge_color'    => 'danger',
                'icono'          => 'bi-pie-chart-fill',
            ],
            [
                'id'             => 2,
                'titulo'         => 'Circuito Termal & Masaje Relajante de 50 min',
                'empresa'        => 'Serenity Spa El Salvador',
                'rubro'          => 'Belleza y Bienestar',
                'precio_regular' => 60.00,
                'precio_oferta'  => 29.99,
                'descuento'      => 50,
                'fecha_limite'   => date('Y-m-d', strtotime('+20 days')),
                'descripcion'    => 'Sesión completa de hidroterapia, sauna seco y masaje descontracturante con aceites aromáticos.',
                'badge_color'    => 'success',
                'icono'          => 'bi-heart-pulse-fill',
            ],
            [
                'id'             => 3,
                'titulo'         => 'Escapada de Fin de Semana en Costa del Sol',
                'empresa'        => 'Hotel & Resort Sol Marino',
                'rubro'          => 'Turismo y Hotel',
                'precio_regular' => 140.00,
                'precio_oferta'  => 84.00,
                'descuento'      => 40,
                'fecha_limite'   => date('Y-m-d', strtotime('+30 days')),
                'descripcion'    => 'Estadía para 2 personas con desayunos típicos buffet incluidos y acceso ilimitado a piscinas.',
                'badge_color'    => 'primary',
                'icono'          => 'bi-sun-fill',
            ],
            [
                'id'             => 4,
                'titulo'         => 'Limpieza Dental Ultrasónica + Profilaxis',
                'empresa'        => 'Clínica Dental Santa Elena',
                'rubro'          => 'Salud Dental',
                'precio_regular' => 45.00,
                'precio_oferta'  => 18.00,
                'descuento'      => 60,
                'fecha_limite'   => date('Y-m-d', strtotime('+10 days')),
                'descripcion'    => 'Diagnóstico digitalizado, eliminación de sarro ultrasónico, pulido y aplicación de flúor preventivo.',
                'badge_color'    => 'info',
                'icono'          => 'bi-shield-check',
            ],
        ];

        require __DIR__ . '/../views/home/index.php';
    }
}
