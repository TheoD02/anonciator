import { Authenticated, GitHubBanner, Refine } from "@refinedev/core";
import { DevtoolsPanel, DevtoolsProvider } from "@refinedev/devtools";
import { RefineKbar, RefineKbarProvider } from "@refinedev/kbar";

import {
  ErrorComponent,
  ThemedLayoutV2,
  ThemedSiderV2,
  useNotificationProvider,
} from "@refinedev/antd";
import "@refinedev/antd/dist/reset.css";

import routerBindings, {
  CatchAllNavigate,
  DocumentTitleHandler,
  NavigateToResource,
  UnsavedChangesNotifier,
} from "@refinedev/react-router";
import {
  App as AntdApp,
  Breadcrumb,
  Layout,
  Menu,
  MenuProps,
  theme,
} from "antd";
import {
  BrowserRouter,
  Outlet,
  Route,
  Routes,
  useNavigate,
} from "react-router";
import { authProvider } from "./authProvider";
import { Header } from "./components/header";
import { ColorModeContextProvider } from "./contexts/color-mode";
import { ForgotPassword } from "./pages/forgotPassword";
import { Login } from "./pages/login";
import { Register } from "./pages/register";
import { AnnounceList } from "./pages/announces/list";
import { apiDataProvider } from "./api";
import { AnnounceShow } from "./pages/announces/show";
import { AnnounceEdit } from "./pages/announces/edit";
import { AnnounceCreate } from "./pages/announces/create";
import {
  CategoryCreate,
  CategoryEdit,
  CategoryList,
  CategoryShow,
} from "./pages/categories";
import { Content, Footer } from "antd/es/layout/layout";
import { ItemType } from "antd/lib/menu/interface";
import AdminHeader from "./components/header/admin";
import FrontAnnounceList from "./pages/front/announces/list";

function App() {
  const {
    token: { colorBgContainer, borderRadiusLG },
  } = theme.useToken();

  return (
    <BrowserRouter>
      <RefineKbarProvider>
        <ColorModeContextProvider>
          <AntdApp>
            <DevtoolsProvider>
              <Refine
                dataProvider={apiDataProvider(
                  "https://php.anonciator.orb.local/api"
                )}
                notificationProvider={useNotificationProvider}
                routerProvider={routerBindings}
                authProvider={authProvider}
                resources={[
                  {
                    name: "announces_parent",
                    meta: {
                      label: "Announces",
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
                      parent: "announces_parent",
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
                      parent: "announces_parent",
                    },
                  },
                ]}
                options={{
                  syncWithLocation: true,
                  warnWhenUnsavedChanges: true,
                  useNewQueryKeys: true,
                  projectId: "553Gaj-ZzupEf-3JY7jD",
                }}
              >
                <Routes>
                  <Route
                    path="/*"
                    element={
                      <Layout>
                        <AdminHeader />
                        <Content
                          style={{ padding: "0 24px", minHeight: "100vh" }}
                        >
                          <Outlet />
                        </Content>
                      </Layout>
                    }
                  >
                    <Route path="announces" element={<FrontAnnounceList />} />
                  </Route>
                  <Route
                    path="/admin"
                    element={
                      <Authenticated
                        key="authenticated-inner"
                        fallback={<CatchAllNavigate to="/login" />}
                      >
                        <ThemedLayoutV2
                          Header={Header}
                          Sider={(props) => <ThemedSiderV2 {...props} fixed />}
                        >
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
                      <Route path="show/:id" element={<AnnounceShow />} />
                      <Route path="edit/:id" element={<AnnounceEdit />} />
                    </Route>
                    <Route path="/admin/announces/categories">
                      <Route index element={<CategoryList />} />
                      <Route path="create" element={<CategoryCreate />} />
                      <Route path="show/:id" element={<CategoryShow />} />
                      <Route path="edit/:id" element={<CategoryEdit />} />
                    </Route>
                    <Route path="*" element={<ErrorComponent />} />
                  </Route>
                  <Route
                    element={
                      <Authenticated
                        key="authenticated-outer"
                        fallback={<Outlet />}
                      >
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
              <DevtoolsPanel />
            </DevtoolsProvider>
          </AntdApp>
        </ColorModeContextProvider>
      </RefineKbarProvider>
    </BrowserRouter>
  );
}

export default App;
