import {Authenticated, GitHubBanner, Refine} from "@refinedev/core";
import {DevtoolsPanel, DevtoolsProvider} from "@refinedev/devtools";
import {RefineKbar, RefineKbarProvider} from "@refinedev/kbar";

import {ErrorComponent, ThemedLayoutV2, ThemedSiderV2, useNotificationProvider,} from "@refinedev/antd";
import "@refinedev/antd/dist/reset.css";

import routerBindings, {
  CatchAllNavigate,
  DocumentTitleHandler,
  NavigateToResource,
  UnsavedChangesNotifier,
} from "@refinedev/react-router";
import {App as AntdApp} from "antd";
import {BrowserRouter, Outlet, Route, Routes} from "react-router";
import {authProvider} from "./authProvider";
import {Header} from "./components/header";
import {ColorModeContextProvider} from "./contexts/color-mode";
import {ForgotPassword} from "./pages/forgotPassword";
import {Login} from "./pages/login";
import {Register} from "./pages/register";
import {AnnounceList} from "./pages/announces/list";
import {apiDataProvider} from "./api";
import {AnnounceShow} from "./pages/announces/show";
import {AnnounceEdit} from "./pages/announces/edit";
import {AnnounceCreate} from "./pages/announces/create";

function App() {
  return (
    <BrowserRouter>
      <GitHubBanner/>
      <RefineKbarProvider>
        <ColorModeContextProvider>
          <AntdApp>
            <DevtoolsProvider>
              <Refine
                dataProvider={apiDataProvider("https://anonciator.api.localhost/api")}
                notificationProvider={useNotificationProvider}
                routerProvider={routerBindings}
                authProvider={authProvider}
                resources={[
                  {
                    name: "announces",
                    list: "/announces",
                    create: "/announces/create",
                    edit: "/announces/edit/:id",
                    show: "/announces/show/:id",
                    meta: {
                      canDelete: true,
                    },
                  }
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
                    element={
                      <Authenticated
                        key="authenticated-inner"
                        fallback={<CatchAllNavigate to="/login"/>}
                      >
                        <ThemedLayoutV2
                          Header={Header}
                          Sider={(props) => <ThemedSiderV2 {...props} fixed/>}
                        >
                          <Outlet/>
                        </ThemedLayoutV2>
                      </Authenticated>
                    }
                  >
                    <Route
                      index
                      element={<NavigateToResource resource="announces"/>}
                    />
                    <Route path="/announces">
                      <Route index element={<AnnounceList/>}/>
                      <Route path="create" element={<AnnounceCreate/>}/>
                      <Route path="show/:id" element={<AnnounceShow/>}/>
                      <Route path="edit/:id" element={<AnnounceEdit/>}/>
                    </Route>
                    <Route path="*" element={<ErrorComponent/>}/>
                  </Route>
                  <Route
                    element={
                      <Authenticated
                        key="authenticated-outer"
                        fallback={<Outlet/>}
                      >
                        <NavigateToResource/>
                      </Authenticated>
                    }
                  >
                    <Route path="/login" element={<Login/>}/>
                    <Route path="/register" element={<Register/>}/>
                    <Route
                      path="/forgot-password"
                      element={<ForgotPassword/>}
                    />
                  </Route>
                </Routes>

                <RefineKbar/>
                <UnsavedChangesNotifier/>
                <DocumentTitleHandler/>
              </Refine>
              <DevtoolsPanel/>
            </DevtoolsProvider>
          </AntdApp>
        </ColorModeContextProvider>
      </RefineKbarProvider>
    </BrowserRouter>
  );
}

export default App;
