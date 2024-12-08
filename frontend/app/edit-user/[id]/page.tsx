"use client";

import DynamicForm from "@/components/form";
import { StandardResponse } from "@/interface/standard-response";
import { getRequest } from "@/lib/utils";
import { use, useEffect, useState } from "react";
import { FieldValues } from "react-hook-form";

export default function Page({ params }: { params: Promise<{ id: string }> }) {
  const handleSubmit = async (data: FieldValues) => {
    console.log(data);
  };
  const [formLoading, setFormLoading] = useState<boolean>(true);
  const [formSchema, setformSchema] = useState({});
  const [isSubmitEnabled, setIsSubmitEnabled] = useState(true);
  const [values, setValues] = useState<FieldValues>({});

  const USER_DETAIL_URL = "http://localhost:8080/v1/user";
  const FORM_URL = "http://localhost:8080/v1/user/form";

  const unwrappedParams = use(params);
  useEffect(() => {
    setFormLoading(true);
    const fetchData = async () => {
      const form_data: StandardResponse = await getRequest(FORM_URL);
      setformSchema(form_data.data);
      const data: StandardResponse = await getRequest(
        `${USER_DETAIL_URL}/${unwrappedParams.id}`
      );
      setValues(data.data)
      setFormLoading(false);
    };
    fetchData();
  }, []);

  if (formLoading) {
    return (
      <div className="flex justify-center items-center w-full h-64">
        <div className="border-t-4 border-gray-500 border-solid rounded-full w-8 h-8 animate-spin"></div>
      </div>
    );
  }
  return (
    <div className="p-8 flex flex-col space-y-4">
      <h1 className="text-xl">Edit User Form</h1>
      <DynamicForm
        schema={formSchema}
        onSubmit={handleSubmit}
        is_enabled={isSubmitEnabled}
        values={values}
      />
    </div>
  );
}
