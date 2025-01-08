import {Edit, useForm, useSelect} from "@refinedev/antd";
import {useApiUrl, useDelete, useMany} from "@refinedev/core";
import MDEditor from "@uiw/react-md-editor";
import {Form, Input, InputNumber, Select, Upload} from "antd";
import {UploadFile} from "antd/lib";

export const AnnounceEdit = () => {
  const {formProps, saveButtonProps, query, formLoading, onFinish} = useForm({});

  const blogPostsData = query?.data?.data;

  const {selectProps: categorySelectProps} = useSelect({
    resource: "announces/categories",
    defaultValue: blogPostsData?.categoryId,
    optionLabel: "name",
    queryOptions: {
      enabled: !!blogPostsData?.categoryId,
    },
  });

  const apiUrl = useApiUrl();
  const {mutate: deleteResource} = useDelete();
  const handleOnFinish = (values) => {
    const photos = values.photos?.fileList || blogPostsData?.photoIds?.map((id) => ({uid: id})) || [];
    onFinish({
      ...values,
      photos: {
        set: photos.map((photo) => photo.response?.data?.id || photo.uid).filter(Boolean),
      },
    });
  };
  type Photo = {
    id: string;
    originalName: string;
    path: string;
    bucket: string;
  }
  const {data: photos, isLoading: isLoadingPhotos} = useMany<Photo>({
    resource: "resources",
    ids: blogPostsData?.photoIds ?? [],
    queryOptions: {
      enabled: blogPostsData !== undefined && blogPostsData?.photoIds?.length > 0,
    },
  });

  const defaultFileList: UploadFile[] = photos?.data.map((photo) => ({
    uid: photo.id,
    name: photo.originalName,
    status: "done",
    url: `https://anonciator.api.localhost/api/resources/${photo.id}`,
  })) ?? [];

  return (
    <Edit saveButtonProps={saveButtonProps} isLoading={formLoading}>
      <Form {...formProps} onFinish={handleOnFinish} layout="vertical">
        <Form.Item
          label={"Title"}
          name={["title"]}
          rules={[
            {
              required: true,
            },
          ]}
        >
          <Input/>
        </Form.Item>
        <Form.Item
          label={"Description"}
          name="description"
          rules={[
            {
              required: true,
            },
          ]}
        >
          <MDEditor/>
        </Form.Item>
        <Form.Item
          label={"Category"}
          name={["category", "set"]}
          initialValue={[formProps?.initialValues?.categoryId]}
          rules={[
            {
              required: true,
            },
          ]}
        >
          <Select {...categorySelectProps} />
        </Form.Item>
        <Form.Item
          label={"Price"}
          name={["price"]}
          rules={[
            {
              required: true,
            },
          ]}
        >
          <InputNumber/>
        </Form.Item>
        <Form.Item
          label={"Location"}
          name={["location"]}
          rules={[
            {
              required: true,
            },
          ]}
        >
          <InputNumber/>
        </Form.Item>
        <Form.Item
          label={"Status"}
          name={["status"]}
          initialValue={"draft"}
          rules={[
            {
              required: true,
            },
          ]}
        >
          <Select
            defaultValue={"draft"}
            options={[
              {value: "draft", label: "Draft"},
              {value: "published", label: "Published"},
              {value: "rejected", label: "Rejected"},
            ]}
            style={{width: 120}}
          />
        </Form.Item>
        <Form.Item label="Photos">
          <Form.Item
            name="photos"
            valuePropName="photos"
            noStyle
          >
            {isLoadingPhotos && blogPostsData?.photoIds?.length !== 0 && <p>Loading...</p>}
            {(!isLoadingPhotos || blogPostsData?.photoIds?.length === 0) && (
              <Upload.Dragger
                name="file"
                defaultFileList={defaultFileList}
                action={`${apiUrl}/resources`}
                listType="picture"
                maxCount={5}
                multiple
                onRemove={(file) => {
                  deleteResource({id: file.uid, resource: "resources"});
                }}
              >
                <p className="ant-upload-text">
                  Glissez-déposez des fichiers ou cliquez pour télécharger
                </p>
              </Upload.Dragger>
            )}
          </Form.Item>
        </Form.Item>
      </Form>
    </Edit>
  );
};
