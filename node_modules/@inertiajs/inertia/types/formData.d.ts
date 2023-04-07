import { FormDataConvertible } from './types';
export declare function objectToFormData(source: Record<string, FormDataConvertible>, form?: FormData, parentKey?: string | null): FormData;
