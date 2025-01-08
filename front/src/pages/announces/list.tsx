import {DeleteButton, EditButton, List, ShowButton, useTable,} from "@refinedev/antd";
import {Space, Table} from "antd";
import {BaseRecord, useMany} from "@refinedev/core";

export const AnnounceList = () => {
  const {tableProps} = useTable({
    syncWithLocation: true,
  });

  const {data: categoryData, isLoading: categoryIsLoading} = useMany({
    resource: "announces/categories",
    ids:
      tableProps?.dataSource
        ?.map((item) => item?.categoryId)
        .filter(Boolean) ?? [],
    queryOptions: {
      enabled: !!tableProps?.dataSource,
    },
  });

  return (
    <List>
      <Table {...tableProps} rowKey="id">
        <Table.Column dataIndex="id" title={"ID"}/>
        <Table.Column dataIndex="title" title={"Title"}/>
        <Table.Column
          dataIndex={"categoryId"}
          title={"Category"}
          render={(value) =>
            categoryIsLoading ? (
              <>Loading...</>
            ) : (
              categoryData?.data?.find((item) => item.id === value)?.name
            )
          }
        />
        <Table.Column dataIndex="price" title={"Price"}/>
        <Table.Column dataIndex="status" title={"Status"}/>
        <Table.Column
          title={"Actions"}
          dataIndex="actions"
          render={(_, record: BaseRecord) => (
            <Space>
              <EditButton hideText size="small" recordItemId={record.id} />
              <ShowButton hideText size="small" recordItemId={record.id} />
              <DeleteButton hideText size="small" recordItemId={record.id} />
            </Space>
          )}
        />
      </Table>
    </List>
  );
};
