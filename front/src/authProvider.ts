import { type AuthProvider } from "@refinedev/core";

export const TOKEN_KEY = "refine-auth";

export const authProvider: AuthProvider = {
  login: async ({ username, email, password }) => {
    const res = await fetch('https://php.anonciator.orb.local/api/login_check', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ username: email, password }),
    });
    const json = await res.json();

    if (res.ok) {
      localStorage.setItem(TOKEN_KEY, json.token);
      return {
        success: true,
        redirectTo: "/",
      };
    }

    return {
      success: false,
      error: {
        name: "LoginError",
        message: "Invalid username or password",
      },
    };
  },
  logout: async () => {
    localStorage.removeItem(TOKEN_KEY);

    return {
      success: true,
      redirectTo: "/login",
    };
  },
  check: async () => {
    const token = localStorage.getItem(TOKEN_KEY);
    if (!token) {
      return {
        authenticated: false,
        redirectTo: "/login",
      };
    }

    const decoded = JSON.parse(atob(token.split(".")[1]));

    if (decoded.exp < Date.now() / 1000) {
      return {
        authenticated: false,
        redirectTo: "/login",
      };
    }

    if (token) {
      return {
        authenticated: true,
      };
    }

    return {
      authenticated: false,
      redirectTo: "/login",
    };
  },
  getPermissions: async () => null,
  getIdentity: async () => {
    const token = localStorage.getItem(TOKEN_KEY);
    const decoded = JSON.parse(atob(token.split(".")[1]));

    if (token) {
      return {
        id: 1,
        name: decoded.email,
        avatar: "https://i.pravatar.cc/300",
      };
    }

    return null;
  },
  onError: async (error) => {
    if (error.response?.status === 401) {
      return {
        logout: true,
      };
    }

    return { error };
  },
};
