import { apiClient } from './http';

export type TaskStatus = 'todo' | 'in_progress' | 'done';
export type TaskPriority = 'low' | 'medium' | 'high';

export interface Task {
    id: number;
    project_id: number;
    created_by: number;
    assigned_to: number | null;
    title: string;
    description: string | null;
    status: TaskStatus;
    priority: TaskPriority;
    due_date: string | null;
    created_at: string;
    updated_at: string;
}

export interface TaskPayload {
    title: string;
    description: string | null;
    status: TaskStatus;
    priority: TaskPriority;
    assigned_to: number | null;
    due_date: string | null;
}

interface ResourceResponse<T> {
    data: T;
}

interface CollectionResponse<T> {
    data: T[];
}

export async function listTasks(projectId: number): Promise<Task[]> {
    return (await apiClient.get<CollectionResponse<Task>>(`/projects/${projectId}/tasks`)).data.data;
}

export async function createTask(projectId: number, payload: TaskPayload): Promise<Task> {
    return (await apiClient.post<ResourceResponse<Task>>(`/projects/${projectId}/tasks`, payload)).data.data;
}

export async function updateTask(projectId: number, taskId: number, payload: TaskPayload): Promise<Task> {
    return (await apiClient.patch<ResourceResponse<Task>>(`/projects/${projectId}/tasks/${taskId}`, payload)).data.data;
}

export async function deleteTask(projectId: number, taskId: number): Promise<void> {
    await apiClient.delete(`/projects/${projectId}/tasks/${taskId}`);
}
