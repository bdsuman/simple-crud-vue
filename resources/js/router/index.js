import { createWebHistory, createRouter } from "vue-router";
import { trans, loadLanguageAsync } from "laravel-vue-i18n";
import { useUserStore } from "@/stores/useUserStore";
import userRoutes from "./users";
import session from "./session";
import music_management from "./music_management";
import componentsRoutes from "./components";
import profiling from "./profiling";
import testimonial from "./testimonial";
import feedback from "./feedback";
import background_image from "./background_image";
import development_journey from "./development_journey";
import journal from "./journal";
import journal_user_response from "./journal_user_response";
import expert from "./expert";

const AuthenticatedLayout = () => import("@/layouts/AuthenticatedLayout.vue");

const routes = [
    {
        path: "/",
        component: AuthenticatedLayout,
        children: [
            {
                path: "/",
                name: "home",
                component: () => import("@/pages/users/Index.vue"),
                meta: {
                    requiresAuth: true,
                    title: "Home",
                },
            },
        ],
    },
    ...userRoutes,
    ...componentsRoutes,
    ...profiling,
    ...session,
    ...testimonial,
    ...music_management,
    ...feedback,
    ...background_image,
    ...development_journey,
    ...journal,
    ...journal_user_response,
    ...expert,
    // {
    //   path: "/",
    // },
    {
        path: "/login",
        name: "login",
        component: () => import("@/pages/auth/Login.vue"),
        meta: {
            requiresAuth: false,
            title: "Login",
        },
    },
    {
        path: "/test",
        name: "test",
        component: () => import("@/pages/test/TestComponent.vue"),
        meta: {
            requiresAuth: false,
        },
    },
    {
        path: "/error",
        name: "Error",
        component: () => import("@/pages/errors/Index.vue"),
        meta: {
            requiresAuth: false,
            title: "Error",
        },
    },
    {
        path: "/:pathMatch(.*)*",
        redirect: (to) => ({
            path: "/error",
            query: { code: 404, message: "Not Found", from: to.fullPath },
        }),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// for authenticated routes
router.beforeEach(async (to, from) => {
    const store = useUserStore();
    await loadLanguageAsync(store?.language ?? "de");

    // Set page title
    document.title = trans(to.meta.title ?? "Deep Grow");

    // Not authenticated and requires auth → go to login
    if (to.meta.requiresAuth && store.token == 0) {
        return { name: "login" };
    }

    // If already logged in and visiting a public route
    // Exclude error + not-found redirection
    if (
        to.meta.requiresAuth === false &&
        store.token != 0 &&
        to.name !== "Error"
    ) {
        return { name: "home" };
    }

    // Restrict dummy components route for non-dev users
    if (to.path.startsWith("/dummy-components") && store.user?.role !== "dev") {
        return {
            path: "/error",
            query: { code: 403, message: "Forbidden", from: to.fullPath },
        };
    }
});

export default router;
