using Microsoft.EntityFrameworkCore.Migrations;
using Npgsql.EntityFrameworkCore.PostgreSQL.Metadata;

namespace AuthService.Data.Migrations;

public partial class InitialCreate : Migration
{
    protected override void Up(MigrationBuilder migrationBuilder)
    {
        migrationBuilder.CreateTable(
            name: "Users",
            columns: table => new
            {
                Id           = table.Column<int>(nullable: false).Annotation("Npgsql:ValueGenerationStrategy", NpgsqlValueGenerationStrategy.IdentityByDefaultColumn),
                Name         = table.Column<string>(maxLength: 100, nullable: false),
                Email        = table.Column<string>(maxLength: 150, nullable: false),
                PasswordHash = table.Column<string>(nullable: false),
                Role         = table.Column<string>(maxLength: 20, nullable: false, defaultValue: "user"),
                IsActive     = table.Column<bool>(nullable: false, defaultValue: true),
                CreatedAt    = table.Column<DateTime>(nullable: false, defaultValueSql: "NOW()"),
                UpdatedAt    = table.Column<DateTime>(nullable: false, defaultValueSql: "NOW()")
            },
            constraints: table => table.PrimaryKey("PK_Users", x => x.Id));

        migrationBuilder.CreateIndex(name: "IX_Users_Email", table: "Users", column: "Email", unique: true);
    }

    protected override void Down(MigrationBuilder migrationBuilder)
    {
        migrationBuilder.DropTable(name: "Users");
    }
}
