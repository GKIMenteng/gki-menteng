// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import { getFirestore } from "firebase/firestore";
import { getAuth } from "firebase/auth";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyBDm4xXDdKLuo9eUVdMEzJPTjnQDfj0C18",
  authDomain: "gkimenteng-7.firebaseapp.com",
  projectId: "gkimenteng-7",
  storageBucket: "gkimenteng-7.firebasestorage.app",
  messagingSenderId: "325559498838",
  appId: "1:325559498838:web:c9f5c010e27df75e77e3f7",
  measurementId: "G-NZSBBT3T45",
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const db = getFirestore(app);
const auth = getAuth(app);

export { db, auth };
