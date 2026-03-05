/**
 * Helper to get product image URL.
 * Handles both:
 * 1. Filament FileUpload: saves as array of path strings in product.images column
 *    e.g. product.images = ["products/01K7A59EX5G...png"]
 * 2. ProductImage relation: objects with image_url accessor
 *    e.g. product.images = [{ id: 1, image: "...", image_url: "http://..." }]
 */
export function getProductImage(
    product: any,
    fallback = 'https://placehold.co/400x400/f3f4f6/9ca3af?text=Sin+imagen'
): string {
    if (!product?.images || product.images.length === 0) {
        return fallback;
    }

    const first = product.images[0];

    // Case 1: plain string path (Filament FileUpload)
    if (typeof first === 'string') {
        return '/storage/' + first;
    }

    // Case 2: ProductImage object with image_url accessor
    if (first && typeof first === 'object') {
        if (first.image_url) return first.image_url;
        if (first.image) return '/storage/' + first.image;
    }

    return fallback;
}

export function getAllProductImages(product: any): string[] {
    if (!product?.images || product.images.length === 0) {
        return [];
    }

    return product.images.map((img: any) => {
        if (typeof img === 'string') return '/storage/' + img;
        if (img?.image_url) return img.image_url;
        if (img?.image) return '/storage/' + img.image;
        return '';
    }).filter(Boolean);
}
