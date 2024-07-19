import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                "osu-lime": "#b3ff66",
                "osu-pink": "#ff66ab",
                "osu-darkorange": "#ff9966",
                "osu-blue": "#66ccff",
                "osu-gray": "#394246",
            },
        },
    },

    plugins: [],
};
