import Vue from 'vue'
import Router from 'vue-router'
import Register from '@/components/Register'
import Login from '@/components/Login'
import IDofEmployee from '@/components/IDofEmployee'
import Restaurant from '@/components/Restaurant'
import FoodManagement from '@/components/FoodManagement'
import OrderManagement from '@/components/OrderManagement'
import EmployeeManagement from '@/components/EmployeeManagement'
import TaskManagement from '@/components/TaskManagement'

Vue.use(Router)

export default new Router({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: '登录',
            component: Login
        },
        {
            path:'/login',
            name: '登录',
            component: Login
        },
        {
            path: '/register',
            name: '注册',
            component: Register
        },
        {
            path: '/employeeid',
            name: '员工id',
            component: IDofEmployee        
        },
        {
            path: '/restaurant',
            name: '饭店',
            component: Restaurant,
            children:[
                {
                    path: 'foodmanagement',
                    name: 'foodmanagement',
                    component: FoodManagement
                },
                {
                    path: 'ordermanagement',
                    name: 'ordermanagement',
                    component: OrderManagement
                },
                {
                    path: 'employeemanagement',
                    name: 'employeemanagement',
                    component: EmployeeManagement
                },
                {
                    path: 'taskmanagement',
                    name: 'taskmanagement',
                    component: TaskManagement
                }
            ]        
        },
    ]
})