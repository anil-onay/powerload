import axios from 'axios';
import { removeCookie } from '@/Utils/helpers';
import { getCookie  } from '@/Utils/helpers.js';
import router from '@/Router'

const createAxiosInstance = () => {

 const instance = axios.create({
   baseURL: 'http://127.0.0.1:8000/api',
   withCredentials: false,
 });

 // Request interceptor
 instance.interceptors.request.use(
   config => {
     // Bearer token'ı ekleyin
     config.headers.Authorization = `Bearer ${ getCookie('_token') }`;
     return config;
   },
   error => {
     return Promise.reject(error);
   }
 );

 // Response interceptor
 instance.interceptors.response.use(
   response => {
     return response.data;
   },
   error => {
     if (error?.response?.status === 401) {
       removeCookie('_token');
       localStorage.removeItem('vuex');
       localStorage.removeItem('_secure__ls__metadata');
       router.push('/');
     }
     return Promise.reject(error.response.data);
   }
 );

 return instance;
};


export default {
  install: (app) => {
      app.provide('axios', createAxiosInstance());
  }
};