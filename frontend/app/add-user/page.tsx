"use client";

import DynamicForm from "@/components/form";
import { StandardResponse } from "@/interface/standard-response";
import { getRequest, postRequest } from "@/lib/utils";
import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import { FieldValues } from "react-hook-form";

import toast, { Toaster, ToastType } from "react-hot-toast";

export default function Home() {
  const router = useRouter();
  const notify = (message: string, type: ToastType) => {
    if (type === "success") {
      toast.success(message);
    } else if (type === "error") {
      toast.error(message);
    } else {
      toast(message); // Fallback to the default toast type
    }
  };

  const handleSubmit = async (data: FieldValues) => {
    setIsSubmitEnabled(false)
    notify("Working", "loading");
    // Create a promise that wraps the async operation
    const promise = postRequest(ADD_USER_URL, data);
    try {
      const result: StandardResponse = await promise;
      if (result.code !== 1) {
        notify(result.message, "error");
        setIsSubmitEnabled(true)
      } else {
        notify(result.message, "success");
        router.back();
      }
    } catch (error) {
      console.error(error);
    }
  };
  const [formLoading, setFormLoading] = useState<boolean>(true);
  const [formSchema, setformSchema] = useState({});

  const [isSubmitEnabled, setIsSubmitEnabled] = useState(true);

  const FORM_URL = "http://localhost:8080/v1/user/form";
  const ADD_USER_URL = "http://localhost:8080/v1/user";

  useEffect(() => {
    setFormLoading(true);
    const fetchData = async () => {
      const data: StandardResponse = await getRequest(FORM_URL);
      setformSchema(data.data);
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
    <div className="p-8">
      <DynamicForm schema={formSchema} onSubmit={handleSubmit} is_enabled={isSubmitEnabled} />
    </div>
  );
}
