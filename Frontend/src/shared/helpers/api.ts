import axios from "axios";

const axiosInstance = axios.create({
  baseURL: `${import.meta.env.VITE_API_BACKEND_URL}/api/v1`,
  headers: { "Content-Type": "application/x-www-form-urlencoded" },
  withCredentials: true,
});

axiosInstance.interceptors.response.use(undefined, async (error) => {
  // if (error.response?.status === 401) {
  //   return axiosInstance(error.config); // Retry original request
  // }

  throw error;
});

export default axiosInstance;
