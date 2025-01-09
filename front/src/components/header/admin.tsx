import { Layout, Menu } from "antd";
import { useNavigate } from "react-router";

type MenuItem = {
  key: string;
  label: string;
  to: string;
};
const items: MenuItem[] = [
  {
    key: "1",
    label: "Announces",
    to: "/announces",
  },
];

export const AdminHeader = () => {
  const navigate = useNavigate();
  return (
    <Layout.Header style={{ display: "flex", alignItems: "center" }}>
      <Menu
        theme="dark"
        mode="horizontal"
        defaultSelectedKeys={["1"]}
        items={items}
        style={{ flex: 1, minWidth: 0 }}
        onClick={(e) => {
          console.log(e);
          navigate(items.find((item) => item.key === e.key)?.to || "/");
        }}
      />
    </Layout.Header>
  );
};

export default AdminHeader;
