import axios from "axios";

// Create axios instance with Laravel API base URL
const api = axios.create({
    baseURL: "/",
    headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: "application/json",
        "Content-Type": "application/json",
    },
    withCredentials: true, // Send cookies with requests
});

// Add CSRF token to all requests
api.interceptors.request.use((config) => {
    // Method 1: Get from meta tag (most reliable)
    let token = null;
    const csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
    if (csrfMetaTag && csrfMetaTag.content) {
        token = csrfMetaTag.content;
    }

    // Method 2: Fallback to other meta tags
    if (!token) {
        const tokenMetaTag = document.querySelector('meta[name="_token"]');
        if (tokenMetaTag && tokenMetaTag.content) {
            token = tokenMetaTag.content;
        }
    }

    // Method 3: Fallback to XSRF-TOKEN cookie (Laravel default)
    if (!token) {
        const cookies = document.cookie.split(";");
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split("=");
            if (name === "XSRF-TOKEN") {
                token = decodeURIComponent(value);
                break;
            }
        }
    }

    // Add token to headers - Laravel expects this header
    if (token) {
        config.headers["X-CSRF-TOKEN"] = token;
    } else {
        console.warn("CSRF token not found in document");
    }

    return config;
});

// Handle response errors
api.interceptors.response.use(
    (response) => response,
    (error) => {
        // Handle CSRF token mismatch (419 status)
        if (error.response?.status === 419) {
            console.error("CSRF token validation failed. Refreshing page...");
            // Show notification before reload
            const message =
                error.response?.data?.message ||
                "Your session has expired. Please try again.";
            console.error("Error:", message);

            // Reload page after a brief delay to ensure user sees the message
            setTimeout(() => {
                window.location.reload();
            }, 500);

            return Promise.reject({
                ...error,
                isCsrfError: true,
                message: "Security token expired. Refreshing page...",
            });
        }

        if (error.response?.status === 401) {
            // Clear authentication data on 401
            localStorage.removeItem("auth_token");
            localStorage.removeItem("user_role");

            // Only redirect if not during login attempt
            const requestUrl = error.config?.url;
            if (requestUrl && !requestUrl.includes("/login")) {
                // Redirect to home/login page for other unauthorized requests
                window.location.href = "/";
            }
        }

        return Promise.reject(error);
    },
);

// Auth API endpoints
export const authAPI = {
    voterLogin: (email, password) =>
        api.post("/voter/login", { email, password }),
    voterLogout: () => api.post("/voter/logout"),
    voterRegister: (data) => api.post("/voter/register", data),
    voterCheck: () => api.get("/voter/auth/check"),
    adminLogin: (email, password) =>
        api.post("/admin/login", { email, password }),
    adminLogout: () => api.post("/admin/logout"),
    adminCheck: () => api.get("/admin/auth/check"),
    googleRedirect: () => (window.location.href = "/voter/auth/google"),
};

// Voter API endpoints
export const voterAPI = {
    getDashboard: () => api.get("/voter/dashboard"),
    getVote: () => api.get("/voter/vote"),
    submitVote: (votes) => api.post("/voter/vote", { votes }),
    getVotes: () => api.get("/voter/votes"),
    getResults: () => api.get("/voter/results"),
    getProfile: () => api.get("/voter/profile"),
    updateProfile: (data) => api.put("/voter/profile", data),
    getElectionStatus: () => api.get("/voter/api/election/status"),
    getElectionResults: () => api.get("/voter/api/election/results"),
    getElectionLive: () => api.get("/voter/api/election/live"),
};

// Admin API endpoints
export const adminAPI = {
    getDashboard: () => api.get("/admin/dashboard"),
    getElections: () => api.get("/admin/elections"),
    createElection: (data) => api.post("/admin/elections", data),
    getElection: (id) => api.get(`/admin/elections/${id}/edit`),
    updateElection: (id, data) => api.put(`/admin/elections/${id}`, data),
    endElection: (id) => api.post(`/admin/elections/${id}/end`),
    deleteElection: (id) => api.delete(`/admin/elections/${id}`),
    getVoters: () => api.get("/admin/voters"),
    deleteVoter: (id) => api.delete(`/admin/voters/${id}`),
    getResults: () => api.get("/admin/results"),
    getResultsForElection: (id) => api.get(`/admin/results/${id}`),
    getAnnouncements: () => api.get("/admin/announcements"),
    createAnnouncement: (data) => api.post("/admin/announcements", data),
    updateAnnouncement: (id, data) =>
        api.put(`/admin/announcements/${id}`, data),
    deleteAnnouncement: (id) => api.delete(`/admin/announcements/${id}`),
};

export default api;
