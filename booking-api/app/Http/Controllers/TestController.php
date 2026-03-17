/**
 * @OA\Get(
 * path="/api/ping",
 * summary="Проверка работы API",
 * tags={"Health"},
 * @OA\Response(
 * response=200,
 * description="Успешный ответ",
 * @OA\JsonContent(
 * @OA\Property(property="status", type="string", example="ok")
 * )
 * )
 * )
 */
public function ping()
{
    return response()->json(['status' => 'ok']);
}