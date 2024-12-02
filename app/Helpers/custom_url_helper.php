function anchor($uri = '', string $title = '', $attributes = '', ?App $altConfig = null): string
{
    // use alternate config if provided, else default one
    $config = $altConfig ?? config(App::class);

    // Verifica se o $uri é false ou não é string
    if (!$uri || !is_string($uri)) {
        // Aqui você pode definir um comportamento padrão, como um URL vazio ou uma mensagem de erro
        $uri = ''; // ou uma URL padrão
    }

    $siteUrl = is_array($uri) ? site_url($uri, null, $config) : (preg_match('#^(\w+:)?//#i', $uri) ? $uri : site_url($uri, null, $config));
    // eliminate trailing slash
    $siteUrl = rtrim($siteUrl, '/');

    if ($title === '') {
        $title = $siteUrl;
    }

    if ($attributes !== '') {
        $attributes = stringify_attributes($attributes);
    }

    return '<a href="' . $siteUrl . '"' . $attributes . '>' . $title . '</a>';
}


$siteUrl = is_array($uri) 
    ? site_url($uri, null, $config) 
    : (preg_match('#^(\w+:)?//#i', $uri ?: '', $matches) ? $uri : site_url($uri, null, $config));
