import { Authenticated, GitHubBanner, Refine } from "@refinedev/core";
import { RefineKbar, RefineKbarProvider } from "@refinedev/kbar";

import {
  ErrorComponent,
  useNotificationProvider,
  RefineThemes,
  ThemedLayoutV2,
} from "@refinedev/mantine";

import {
  type ColorScheme,
  ColorSchemeProvider,
  Global,
  MantineProvider,
} from "@mantine/core";
import { useLocalStorage } from "@mantine/hooks";
import { NotificationsProvider } from "@mantine/notifications";
import routerBindings, {
  CatchAllNavigate,
  DocumentTitleHandler,
  NavigateToResource,
  UnsavedChangesNotifier,
} from "@refinedev/react-router";
import { useTranslation } from "react-i18next";
import { BrowserRouter, Outlet, Route, Routes } from "react-router";
import { authProvider } from "./authProvider";
import { Header } from "./components/header";
import {
  CategoryCreate,
  CategoryEdit,
  CategoryList,
  CategoryShow,
} from "./pages/admin/categories";
import { ForgotPassword } from "./pages/front/forgotPassword";
import { Login } from "./pages/front/login";
import { Register } from "./pages/front/register";
import { apiDataProvider } from "./api";
import { AnnounceCreate, AnnounceEdit, AnnounceList, AnnounceShow } from "./pages/admin/announces";
import { IconCategory, IconNews } from "@tabler/icons-react";
import { AnnounceSearch } from "./pages/front/announces";

function App() {
  const [colorScheme, setColorScheme] = useLocalStorage<ColorScheme>({
    key: "mantine-color-scheme",
    defaultValue: "light",
    getInitialValueInEffect: true,
  });
  const { t, i18n } = useTranslation();

  const toggleColorScheme = (value?: ColorScheme) =>
    setColorScheme(value || (colorScheme === "dark" ? "light" : "dark"));

  const i18nProvider = {
    translate: (key: string, params: object) => t(key, params),
    changeLocale: (lang: string) => i18n.changeLanguage(lang),
    getLocale: () => i18n.language,
  };

  return (
    <BrowserRouter>
      <RefineKbarProvider>
        <ColorSchemeProvider
          colorScheme={colorScheme}
          toggleColorScheme={toggleColorScheme}
        >
          {/* You can change the theme colors here. example: theme={{ ...RefineThemes.Magenta, colorScheme:colorScheme }} */}
          <MantineProvider
            theme={{
              ...RefineThemes.Blue,
              colorScheme: colorScheme,
            }}
            withNormalizeCSS
            withGlobalStyles
          >
            <Global styles={{ body: { WebkitFontSmoothing: "auto" } }} />
            <NotificationsProvider position="top-right">
              <Refine
                dataProvider={apiDataProvider("https://php.anonciator.orb.local/api")}
                notificationProvider={useNotificationProvider}
                routerProvider={routerBindings}
                authProvider={authProvider}
                i18nProvider={i18nProvider}
                resources={[
                  {
                    name: "parent_announces",
                    meta: {
                      label: "Announces",
                      icon: <IconNews />,
                    },
                  },
                  {
                    name: "announces",
                    list: "/admin/announces",
                    create: "/admin/announces/create",
                    edit: "/admin/announces/edit/:id",
                    show: "/admin/announces/show/:id",
                    meta: {
                      label: "Announces",
                      canDelete: true,
                      parent: "parent_announces",
                      icon: <IconNews />,
                    },
                  },
                  {
                    name: "announces/categories",
                    list: "/admin/announces/categories",
                    create: "/admin/announces/categories/create",
                    edit: "/admin/announces/categories/edit/:id",
                    show: "/admin/announces/categories/show/:id",
                    meta: {
                      label: "Categories",
                      canDelete: true,
                      parent: "parent_announces",
                      icon: <IconCategory />,
                    },
                  },
                ]}
                options={{
                  syncWithLocation: true,
                  warnWhenUnsavedChanges: true,
                }}
              >
                <Routes>
                  <Route
                    path="/"
                    element={
                      <ThemedLayoutV2 Header={() => <Header sticky />}>
                        <Outlet />
                      </ThemedLayoutV2>
                    }
                  >
                    <Route index element={<AnnounceSearch />} />
                  </Route>
                  <Route
                    path="/admin"
                    element={
                      <Authenticated
                        key="authenticated-routes"
                        fallback={<CatchAllNavigate to="/login" />}
                      >
                        <ThemedLayoutV2 Header={() => <Header sticky />}>
                          <Outlet />
                        </ThemedLayoutV2>
                      </Authenticated>
                    }
                  >
                    <Route
                      index
                      element={<NavigateToResource resource="announces" />}
                    />
                    <Route path="/admin/announces">
                      <Route index element={<AnnounceList />} />
                      <Route path="create" element={<AnnounceCreate />} />
                      <Route path="edit/:id" element={<AnnounceEdit />} />
                      <Route path="show/:id" element={<AnnounceShow />} />
                    </Route>
                    <Route path="/admin/announces/categories">
                      <Route index element={<CategoryList />} />
                      <Route path="create" element={<CategoryCreate />} />
                      <Route path="edit/:id" element={<CategoryEdit />} />
                      <Route path="show/:id" element={<CategoryShow />} />
                    </Route>
                    <Route path="*" element={<ErrorComponent />} />
                  </Route>
                  <Route
                    element={
                      <Authenticated key="auth-pages" fallback={<Outlet />}>
                        <NavigateToResource />
                      </Authenticated>
                    }
                  >
                    <Route path="/login" element={<Login />} />
                    <Route path="/register" element={<Register />} />
                    <Route
                      path="/forgot-password"
                      element={<ForgotPassword />}
                    />
                  </Route>
                </Routes>

                <RefineKbar />
                <UnsavedChangesNotifier />
                <DocumentTitleHandler />
              </Refine>
            </NotificationsProvider>
          </MantineProvider>
        </ColorSchemeProvider>
      </RefineKbarProvider>
    </BrowserRouter>
  );
}

export default App;
