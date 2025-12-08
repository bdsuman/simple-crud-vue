const AuthenticatedLayout = () => import("@/layouts/AuthenticatedLayout.vue");

export default [
    // Commented out - user pages not yet created
    // {
    //     path: "/users",
    //     component: AuthenticatedLayout,
    //     children: [
    //         {
    //             path: "",
    //             name: "users",
    //             component: () => import("@/pages/users/Index.vue"),
    //             meta: {
    //                 requiresAuth: true,
    //                 title: "User Management",
    //             },
    //         },
    //         {
    //             path: "/users/:id",
    //             name: "UserDetails",
    //             component: () => import("@/pages/users/Details.vue"),
    //             meta: {
    //                 requiresAuth: true,
    //                 title: "User Details",
    //             },
    //         },
    //         {
    //             path: "/users/profile/setup",
    //             name: "UserProfileSetup",
    //             component: () =>
    //                 import("@/pages/users/userProfileSetup/Index.vue"),
    //             meta: {
    //                 requiresAuth: true,
    //                 title: "User Profile Setup",
    //             },
    //         },
    //     ],
    // },
];
