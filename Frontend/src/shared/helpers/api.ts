import axios from "axios";

const axiosInstance = axios.create({
  baseURL: "http://localhost:8080",
});

axiosInstance.interceptors.response.use(undefined, async (error) => {
  if (error.response?.status === 401) {
    return axiosInstance(error.config); // Retry original request
  }

  throw error;
});

export default axiosInstance;
