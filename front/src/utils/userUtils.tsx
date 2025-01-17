import { TOKEN_KEY } from "../authProvider";

type Token = {
    exp: number;
    iat: number;
    email: string;
    roles: string[];
    id: number;
    username: string;
}

let cachedUserInfo: Token | null = null;

export const getUserInfo = () => {
    if (cachedUserInfo) {
        return cachedUserInfo;
    }
    const token = localStorage.getItem(TOKEN_KEY);
    cachedUserInfo = token ? JSON.parse(atob(token.split(".")[1])) : null;

    return cachedUserInfo;
}