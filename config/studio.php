<?php

return [
    'caption' => 'CONTENT LIBRARY',
    'navigation' => [
        ['route' => 'dashboard', 'match' => 'dashboard', 'label' => 'Ringkasan', 'icon' => 'M3 10l9-7 9 7v10H3V10z M9 20v-7h6v7'],
        ['route' => 'series.index', 'match' => ['series.*'], 'label' => 'Library series', 'icon' => 'M4 4h16v16H4z M9 8l7 4-7 4V8z'],
        ['route' => 'settings.api', 'match' => 'settings.*', 'label' => 'Akses API', 'icon' => 'M8 15l8-8 M7 10l-3 3a4 4 0 006 6l3-3 M11 8l3-3a4 4 0 016 6l-3 3'],
    ],
];
