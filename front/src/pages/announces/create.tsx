import {Create, useForm, useSelect} from "@refinedev/antd";
import { useApiUrl, useDelete } from "@refinedev/core";
import MDEditor from "@uiw/react-md-editor";
import {Form, Input, InputNumber, Select, Upload} from "antd";

export const AnnounceCreate = () => {
  const {formProps, saveButtonProps, onFinish} = useForm({});

  const {selectProps: categorySelectProps} = useSelect({
    resource: "announces/categories",
    optionLabel: "name",
  });

  const apiUrl = useApiUrl();
  const { mutate: deleteResource } = useDelete();

  const handleOnFinish = (values) => {
    onFinish({
      ...values,
      category: { set: values.category },
      photos: { set: values.photos?.fileList?.map((photo) => Number(photo.response.data.id)) },
    });
  };

  return (
    <Create saveButtonProps={saveButtonProps}>
      <Form {...formProps} onFinish={handleOnFinish} layout="vertical">
        <Form.Item
          label={"Title"}
          name={["title"]}
          initialValue={"test"}
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
          initialValue={"test"}
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
          initialValue={10}
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
          initialValue={10}
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
            <Upload.Dragger
              name="file"
              action={`${apiUrl}/resources`}
              listType="picture"
              maxCount={5}
              multiple
              onRemove={(file) => {
                deleteResource({id: file.response.data.id, resource: "resources"});
              }}
            >
              <p className="ant-upload-text">
                Glissez-déposez des fichiers ou cliquez pour télécharger
              </p>
            </Upload.Dragger>
          </Form.Item>
        </Form.Item>
      </Form>
    </Create>
  );
};
