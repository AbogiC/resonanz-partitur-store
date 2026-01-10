import { createRouter, createWebHistory } from "vue-router";
import Home from "@/views/Home.vue";
import Products from "@/views/Products.vue";

const routes = [
  {
    path: "/",
    name: "Home",
    component: Home,
  },
  {
    path: "/products",
    name: "Products",
    component: Products,
  },
  {
    path: "/login",
    name: "Login",
    component: () => import("@/views/Login.vue"),
  },
  {
    path: "/register",
    name: "Register",
    component: () => import("@/views/Register.vue"),
  },
  {
    path: "/profile",
    name: "Profile",
    component: () => import("@/views/Profile.vue"),
  },
  {
    path: "/orders",
    name: "Orders",
    component: () => import("@/views/Orders.vue"),
  },
  {
    path: "/contact",
    name: "Contact",
    component: () => import("@/views/Contact.vue"),
  },
  {
    path: "/faq",
    name: "FAQ",
    component: () => import("@/views/FAQ.vue"),
  },
  {
    path: "/shipping",
    name: "Shipping",
    component: () => import("@/views/Shipping.vue"),
  },
  {
    path: "/returns",
    name: "Returns",
    component: () => import("@/views/Returns.vue"),
  },
  {
    path: "/privacy",
    name: "Privacy",
    component: () => import("@/views/Privacy.vue"),
  },
  {
    path: "/wishlist",
    name: "Wishlist",
    component: () => import("@/views/Wishlist.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
