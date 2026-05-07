/** @type {import('tailwindcss').Config} */
export default {
    content: ["./resources/**/*.blade.php", "./resources/**/*.js"],
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: "#2563eb",
                    50: "#eff6ff",
                    100: "#dbeafe",
                    500: "#3b82f6",
                    600: "#2563eb",
                    700: "#1d4ed8",
                },
                sidebar: { DEFAULT: "#0f172a", light: "#1e293b" },
            },
            fontFamily: { sans: ["Inter", "ui-sans-serif", "system-ui"] },
        },
    },
    plugins: [require("tailwind-scrollbar-hide")],
};
