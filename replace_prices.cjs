const fs = require('fs');
const path = require('path');

const filePaths = [
    'resources/js/Pages/Home.vue',
    'resources/js/Pages/Products/Index.vue',
    'resources/js/Pages/Products/Show.vue',
    'resources/js/Components/CartSidebar.vue',
    'resources/js/Pages/Cart/Index.vue',
    'resources/js/Pages/Tags/Show.vue',
    'resources/js/Pages/Checkout/Index.vue',
];

filePaths.forEach((filePath) => {
    let content = fs.readFileSync(filePath, 'utf-8');

    // Maches €{{ variables }} or US$ {{ variables.toFixed() }}
    // Group 1: variable expression

    // Specifically for  "€{{ product.price }}"
    content = content.replace(/€{{\s*(.*?)\s*}}/g, '{{ $formatCurrency($1) }}');

    // For "US$ {{ expression }}"
    content = content.replace(/US\$\s*{{\s*(.*?)\s*}}/g, '{{ $formatCurrency($1) }}');

    // Remove .toFixed(2) if it exists, since $formatCurrency handles it
    // Note: JS replace needs care because the first regex already replaced €{{ expr }}.
    // But inside {{ $formatCurrency(X) }}, X might be "(item.price * item.quantity).toFixed(2)"
    content = content.replace(/\$formatCurrency\((.*?)\.toFixed\(\d+\)\)/g, '$formatCurrency($1)');

    fs.writeFileSync(filePath, content, 'utf-8');
});

console.log('Prices replaced successfully');
