import { AuthPage } from "@refinedev/mantine";

export const Login = () => {
  return (
    <AuthPage
      type="login"
      formProps={{
        initialValues: {
          email: "admin@domain.tld",
          password: "admin",
        },
      }}
    />
  );
};
