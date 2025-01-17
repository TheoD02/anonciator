import { Box, Button, Flex, Group, Header, Menu } from "@mantine/core"
import { useNavigate } from "react-router";
import { TOKEN_KEY } from "../../authProvider";

export const FrontHeader = () => {
    const navigate = useNavigate();
    const token = localStorage.getItem(TOKEN_KEY);
    const userDecoded = token ? JSON.parse(atob(token.split(".")[1])) : null;
    const isAdmin = userDecoded?.roles.includes("ROLE_ADMIN");


    return <Header height={60} p="xs">
        <Flex
            align="center"
            justify="space-between"
            style={{ width: "100%" }}
        >
            <Box>
                <Group>
                    <Button
                        onClick={() => navigate("/")}
                    >
                        Home
                    </Button>
                </Group>
            </Box>
            <Box>
                <Group>
                    {userDecoded && (
                        <Menu>
                            <Menu.Target>
                                <Button>
                                    {userDecoded.email}
                                </Button>
                            </Menu.Target>
                            <Menu.Dropdown>
                                <Menu.Label>
                                    Account
                                </Menu.Label>
                                <Menu.Item
                                    onClick={() => navigate("/conversations")}
                                >
                                    Messages
                                </Menu.Item>
                                <Menu.Label>
                                    Actions
                                </Menu.Label>
                                {isAdmin && (
                                    <Menu.Item
                                        onClick={() => navigate("/admin")}
                                    >
                                        Admin
                                    </Menu.Item>
                                )}
                                <Menu.Item
                                    onClick={() => {
                                        localStorage.removeItem(TOKEN_KEY);
                                        navigate("/login");
                                    }}
                                    color="red"
                                >
                                    Logout
                                </Menu.Item>
                            </Menu.Dropdown>
                        </Menu>
                    )}
                    {!userDecoded && (
                        <Button
                            onClick={() => navigate("/login")}
                        >
                            Login
                        </Button>
                    )}
                    {!userDecoded && (
                        <Button
                            onClick={() => navigate("/register")}
                        >
                            Register
                        </Button>
                    )}
                </Group>
            </Box>
        </Flex>
    </Header>
}