import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '../views/LoginView.vue'

const routes = [
  {
    path: '/',
    component: LoginView
  },

  {
    path:'/register',
    component:() =>
      import('../views/RegisterView.vue')
  },

  {
    path:'/verify-otp',
    component:() =>
      import('../views/VerifyOtpView.vue')
  },

  {
    path: '/dashboard',
    component: () =>
      import('../views/admin/DashboardView.vue'),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/activities',
    component: () =>
      import('../views/admin/ActivitiesView.vue'),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/activities/:id',
    component: () =>
      import('../views/admin/ActivityDetailView.vue'),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/funds',
    component: () =>
      import('../views/admin/FundsView.vue'),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/scrap-sales',
    component: () =>
      import('../views/admin/ScrapSalesView.vue'),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/donations',
    component: () =>
      import('../views/admin/DonationsView.vue'),
    meta: {
      requiresAuth: true
    }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})
  

router.beforeEach((to, from, next) => {

    const token = localStorage.getItem('token')

    if (to.meta.requiresAuth && !token) {
        next('/')
    }

    else if (to.path === '/' && token) {
        next('/dashboard')
    }

    else {
        next()
    }

})

export default router