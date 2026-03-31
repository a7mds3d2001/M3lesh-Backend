import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#e6eaf5',
                    100: '#ccd5eb',
                    200: '#99abe7',
                    300: '#6681e3',
                    400: '#3357df',
                    500: '#001554',
                    600: '#001246',
                    700: '#000e38',
                    800: '#000a2b',
                    900: '#00071d',
                    950: '#000410',
                },
                secondary: {
                    50: '#fff8e6',
                    100: '#ffefcc',
                    200: '#ffdf99',
                    300: '#ffcf66',
                    400: '#ffbf33',
                    500: '#ff7300',
                    600: '#e66600',
                    700: '#cc5a00',
                    800: '#994400',
                    900: '#662e00',
                    950: '#331700',
                },
            },
            fontFamily: {
                sans: ['Lama Sans', 'Lato', 'Inter', 'ui-sans-serif', 'system-ui'],
            },
        },
    },
}

