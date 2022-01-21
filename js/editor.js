wp.domReady(() => {
    wp.blocks.unregisterBlockStyle(
        'core/button',
        ['default', 'outline', 'squared', 'fill']
    );

    wp.blocks.registerBlockStyle(
        'core/button',
        [
            {
                name: 'squared',
                label: 'Squared',
                isDefault: true,
            },
            {
                name: 'rounded',
                label: 'Rounded',
            }
        ]
    );
});